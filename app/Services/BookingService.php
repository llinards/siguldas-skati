<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Events\BookingDatesChanged;
use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Minutes a pending hold is kept before it is released.
     */
    public const HOLD_MINUTES = 30;

    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
        private readonly StripeService $stripe,
    ) {}

    /**
     * Create a pending booking that holds the dates while the guest pays.
     *
     * @param  array{sauna_jacuzzi?: bool, baby_cot?: bool}  $requests  Optional
     *                                                                  extras the guest asked for; recorded (not charged) for admin follow-up.
     * @param  array{name: string, email: string, phone: string}  $guest
     */
    public function createPendingBooking(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        int $adults,
        int $children,
        array $requests,
        array $guest,
    ): Booking {
        return DB::transaction(function () use ($product, $checkIn, $checkOut, $adults, $children, $requests, $guest) {
            // Lock the product row to serialize concurrent booking attempts for this house.
            $product = Product::whereKey($product->getKey())->lockForUpdate()->firstOrFail();

            $this->assertGuestCount($product, $adults, $children);

            if (! $this->availability->isAvailable($product, $checkIn, $checkOut)) {
                throw BookingException::datesUnavailable();
            }

            $quote = $this->pricing->quote($product, $checkIn, $checkOut);

            if ($quote->nights < $product->min_nights) {
                throw BookingException::belowMinimumNights($product->min_nights);
            }

            return $product->bookings()->create([
                'reference' => Booking::generateReference(),
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'children' => $children,
                'wants_sauna_jacuzzi' => (bool) ($requests['sauna_jacuzzi'] ?? false),
                'wants_baby_cot' => (bool) ($requests['baby_cot'] ?? false),
                'guest_name' => $guest['name'],
                'guest_email' => $guest['email'],
                'guest_phone' => $guest['phone'],
                'nights_total' => $quote->nightsTotal,
                'grand_total' => $quote->grandTotal,
                'currency' => 'eur',
                'status' => BookingStatus::Pending,
                'expires_at' => now()->addMinutes(self::HOLD_MINUTES),
                'management_token' => (string) Str::uuid(),
            ]);
        });
    }

    /**
     * Confirm a booking once payment has succeeded. Idempotent.
     */
    public function confirm(Booking $booking, ?string $paymentIntentId = null): void
    {
        if ($booking->status === BookingStatus::Confirmed) {
            return;
        }

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'stripe_payment_intent_id' => $paymentIntentId,
            'expires_at' => null,
        ]);

        BookingConfirmed::dispatch($booking->fresh());
    }

    /**
     * Move a confirmed booking to new dates. Re-checks availability (ignoring
     * this booking's own dates) and minimum nights, then recomputes the stored
     * totals. Money is settled manually — no Stripe charge or refund here.
     */
    public function changeDates(Booking $booking, CarbonInterface $checkIn, CarbonInterface $checkOut): void
    {
        DB::transaction(function () use ($booking, $checkIn, $checkOut) {
            // Lock the product row to serialize concurrent booking attempts for this house.
            $product = Product::whereKey($booking->product_id)->lockForUpdate()->firstOrFail();

            if (! $this->availability->isAvailable($product, $checkIn, $checkOut, ignoreBookingId: $booking->getKey())) {
                throw BookingException::datesUnavailable();
            }

            $quote = $this->pricing->quote($product, $checkIn, $checkOut);

            if ($quote->nights < $product->min_nights) {
                throw BookingException::belowMinimumNights($product->min_nights);
            }

            $booking->update([
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'nights_total' => $quote->nightsTotal,
                'grand_total' => $quote->grandTotal,
            ]);
        });

        BookingDatesChanged::dispatch($booking->fresh());
    }

    /**
     * Refund and cancel a booking. Passing a null amount issues a full refund.
     * Idempotent: a booking already cancelled is left untouched.
     */
    public function cancelAndRefund(Booking $booking, ?int $amount = null, ?string $reason = null): void
    {
        if ($booking->status === BookingStatus::Cancelled) {
            return;
        }

        // Stripe dedupes the actual refund via the idempotency key, so this is
        // safe even if a charge.refunded webhook is racing this same request.
        $refund = $this->stripe->createRefund($booking, $amount);

        if (! $this->claimCancellation($booking, [
            'cancellation_reason' => $reason,
            'refund_amount' => $refund->amount ?? ($amount ?? $booking->grand_total),
            'stripe_refund_id' => $refund->id,
        ])) {
            return;
        }

        BookingCancelled::dispatch($booking->fresh(), true);
    }

    /**
     * Record a refund that already happened on Stripe's side (e.g. a dashboard
     * refund surfaced via the charge.refunded webhook). Idempotent.
     */
    public function reconcileRefund(Booking $booking, int $amountRefunded, ?string $refundId = null): void
    {
        if (! $this->claimCancellation($booking, [
            'refund_amount' => $amountRefunded,
            'stripe_refund_id' => $refundId,
        ])) {
            return;
        }

        BookingCancelled::dispatch($booking->fresh(), true);
    }

    /**
     * Atomically transition a booking to Cancelled exactly once. Returns true
     * only for the caller that actually performed the transition, so racing
     * cancellation paths (guest action + charge.refunded webhook) can't each
     * dispatch the cancellation notifications.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function claimCancellation(Booking $booking, array $attributes): bool
    {
        $claimed = Booking::whereKey($booking->getKey())
            ->where('status', '!=', BookingStatus::Cancelled->value)
            ->update(array_merge($attributes, [
                'status' => BookingStatus::Cancelled->value,
                'cancelled_at' => now(),
                'refunded_at' => now(),
            ]));

        return $claimed > 0;
    }

    private function assertGuestCount(Product $product, int $adults, int $children): void
    {
        // person_count = base (adult) spots. children_count = dedicated child
        // spots; when 0, children share the base spots with the adults.
        if ($adults < 1) {
            throw BookingException::atLeastOneAdult();
        }

        if ($adults > $product->person_count) {
            throw BookingException::tooManyAdults($product->person_count);
        }

        if ($product->children_count > 0) {
            if ($children > $product->children_count) {
                throw BookingException::tooManyChildren($product->children_count);
            }
        } elseif ($adults + $children > $product->person_count) {
            throw BookingException::exceedsCapacity($product->person_count);
        }
    }
}
