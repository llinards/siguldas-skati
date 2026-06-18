<?php

namespace App\Livewire\Admin\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\FlashMessageService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class BookingDetail extends Component
{
    /**
     * Statuses an admin may set by hand. Cancelling here does NOT refund —
     * that stays with the dedicated refund action below.
     */
    private const MANUAL_STATUSES = [BookingStatus::Confirmed, BookingStatus::Cancelled];

    public Booking $booking;

    public ?string $refundReason = null;

    public ?string $notes = null;

    public string $paymentStatus = '';

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['product', 'addons']);
        $this->notes = $booking->notes;
        $this->paymentStatus = in_array($booking->status, self::MANUAL_STATUSES, true)
            ? $booking->status->value
            : BookingStatus::Confirmed->value;
    }

    /**
     * Manually set the payment status. No Stripe call and no customer email —
     * this only changes the stored status and its timestamps.
     */
    public function changeStatus(FlashMessageService $flash): void
    {
        $allowed = array_map(fn (BookingStatus $status) => $status->value, self::MANUAL_STATUSES);

        $this->validate([
            'paymentStatus' => ['required', Rule::in($allowed)],
        ]);

        $status = BookingStatus::from($this->paymentStatus);

        if ($status === $this->booking->status) {
            $flash->error(__('Statuss jau ir iestatīts.'));

            return;
        }

        $attributes = ['status' => $status];

        if ($status === BookingStatus::Confirmed) {
            $attributes['expires_at'] = null;
            $attributes['cancelled_at'] = null;
            $attributes['cancellation_reason'] = null;
        } else {
            $attributes['cancelled_at'] = now();
        }

        $this->booking->update($attributes);
        $this->booking->refresh();

        $flash->success(__('Statuss atjaunināts.'));
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
