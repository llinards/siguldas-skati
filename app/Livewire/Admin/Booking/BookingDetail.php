<?php

namespace App\Livewire\Admin\Booking;

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
