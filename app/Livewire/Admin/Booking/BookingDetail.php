<?php

namespace App\Livewire\Admin\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;

class BookingDetail extends Component
{
    public Booking $booking;

    public ?string $refundReason = null;

    public ?string $notes = null;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['product', 'addons']);
        $this->notes = $booking->notes;
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
