<?php

namespace App\Livewire\Admin\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\FlashMessageService;
use App\Services\PricingService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BookingDetail extends Component
{
    public Booking $booking;

    public ?string $refundReason = null;

    public ?string $notes = null;

    public string $newCheckIn = '';

    public string $newCheckOut = '';

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['product', 'addons']);
        $this->notes = $booking->notes;
        $this->newCheckIn = $booking->check_in->toDateString();
        $this->newCheckOut = $booking->check_out->toDateString();
    }

    /**
     * Move a confirmed booking to new dates. Availability and minimum nights
     * are re-checked; the price is recomputed and the admin is told the
     * difference to settle manually.
     */
    public function changeDates(BookingService $bookings, FlashMessageService $flash): void
    {
        if ($this->booking->status !== BookingStatus::Confirmed) {
            return;
        }

        $this->validate([
            'newCheckIn' => ['required', 'date'],
            'newCheckOut' => ['required', 'date', 'after:newCheckIn'],
        ]);

        $previousTotal = $this->booking->grand_total;

        try {
            $bookings->changeDates($this->booking, Carbon::parse($this->newCheckIn), Carbon::parse($this->newCheckOut));
        } catch (BookingException $e) {
            $flash->error($e->getMessage());

            return;
        }

        $this->booking->refresh();
        $this->newCheckIn = $this->booking->check_in->toDateString();
        $this->newCheckOut = $this->booking->check_out->toDateString();

        $flash->success($this->dateChangeMessage($this->booking->grand_total - $previousTotal));
    }

    /**
     * Live preview of the chosen new dates: price, the difference versus the
     * current total, availability, and whether the minimum-nights rule holds.
     *
     * @return array{nights: int, total: int, difference: int, available: bool, belowMin: bool, minNights: int}|null
     */
    #[Computed]
    public function datePreview(): ?array
    {
        try {
            $checkIn = Carbon::parse($this->newCheckIn)->startOfDay();
            $checkOut = Carbon::parse($this->newCheckOut)->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return null;
        }

        $product = $this->booking->product;
        $quote = app(PricingService::class)->quote($product, $checkIn, $checkOut);

        return [
            'nights' => $quote->nights,
            'total' => $quote->grandTotal,
            'difference' => $quote->grandTotal - $this->booking->grand_total,
            'available' => app(AvailabilityService::class)->isAvailable($product, $checkIn, $checkOut, ignoreBookingId: $this->booking->getKey()),
            'belowMin' => $quote->nights < $product->min_nights,
            'minNights' => $product->min_nights,
        ];
    }

    /**
     * Build the flash message describing the price difference after a move.
     */
    private function dateChangeMessage(int $difference): string
    {
        if ($difference > 0) {
            return __('Datumi mainīti. Jāiekasē papildus:').' '.$this->formatCents($difference);
        }

        if ($difference < 0) {
            return __('Datumi mainīti. Jāatmaksā:').' '.$this->formatCents(-$difference);
        }

        return __('Datumi mainīti. Cena nemainās.');
    }

    private function formatCents(int $cents): string
    {
        return number_format($cents / 100, 2, ',', ' ').' €';
    }

    /**
     * Manually confirm a pending booking (e.g. payment succeeded but the
     * Stripe webhook never arrived). Reuses the normal confirmation flow, so
     * the customer receives their confirmation email and the hold is kept.
     */
    public function confirmBooking(BookingService $bookings, FlashMessageService $flash): void
    {
        if ($this->booking->status !== BookingStatus::Pending) {
            return;
        }

        $bookings->confirm($this->booking);
        $this->booking->refresh();

        $flash->success(__('Rezervācija apstiprināta.'));
    }

    public function refund(BookingService $bookings, FlashMessageService $flash): void
    {
        // Admins may refund at any time; the UI issues a full refund.
        $bookings->cancelAndRefund($this->booking, null, $this->refundReason);
        $this->booking->refresh();

        $flash->success(__('Atmaksa veikta.'));
    }

    public function saveNotes(FlashMessageService $flash): void
    {
        $this->booking->update(['notes' => $this->notes]);

        $flash->success(__('Piezīmes saglabātas.'));
    }

    public function render(): View
    {
        return view('livewire.admin.booking.booking-detail')
            ->layout('layouts.admin.app');
    }
}
