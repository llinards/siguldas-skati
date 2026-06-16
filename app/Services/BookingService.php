<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Exceptions\BookingException;
use App\Models\Addon;
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
    ) {}

    /**
     * Create a pending booking that holds the dates while the guest pays.
     *
     * @param  array<int, array{addon: Addon, quantity: int}>  $addonSelections
     * @param  array{name: string, email: string, phone: string}  $guest
     */
    public function createPendingBooking(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        int $adults,
        int $children,
        array $addonSelections,
        array $guest,
    ): Booking {
        return DB::transaction(function () use ($product, $checkIn, $checkOut, $adults, $children, $addonSelections, $guest) {
            // Lock the product row to serialize concurrent booking attempts for this house.
            $product = Product::whereKey($product->getKey())->lockForUpdate()->firstOrFail();

            $this->assertGuestCount($product, $adults, $children);

            if (! $this->availability->isAvailable($product, $checkIn, $checkOut)) {
                throw BookingException::datesUnavailable();
            }

            $quote = $this->pricing->quote($product, $checkIn, $checkOut, $addonSelections);

            if ($quote->nights < $product->min_nights) {
                throw BookingException::belowMinimumNights($product->min_nights);
            }

            $booking = $product->bookings()->create([
                'reference' => Booking::generateReference(),
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'children' => $children,
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

            // Add-ons are recorded as requests (not charged) so the admin can follow up.
            foreach ($addonSelections as $selection) {
                $addon = $selection['addon'];
                $booking->addons()->attach($addon->id, [
                    'name' => $addon->getTranslation('name', app()->getLocale()),
                    'price' => $addon->price,
                    'pricing_type' => $addon->pricing_type->value,
                    'quantity' => max(1, (int) ($selection['quantity'] ?? 1)),
                ]);
            }

            return $booking;
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

    private function assertGuestCount(Product $product, int $adults, int $children): void
    {
        // children_count is an optional sub-limit; 0 means "no separate children limit".
        if ($product->children_count > 0 && $children > $product->children_count) {
            throw BookingException::tooManyChildren($product->children_count);
        }

        // person_count is the total capacity that adults and children share.
        if ($adults < 1 || $adults + $children > $product->person_count) {
            throw BookingException::tooManyGuests($product->person_count);
        }
    }
}
