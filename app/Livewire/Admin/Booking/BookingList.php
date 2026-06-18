<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    /**
     * Permanently delete a booking and its data. The booking_addon rows are
     * removed by the database cascade.
     */
    public function delete(int $bookingId, FlashMessageService $flash): void
    {
        $booking = Booking::find($bookingId);

        if (! $booking) {
            $flash->error(__('Rezervācija nav atrasta.'));

            return;
        }

        $booking->delete();

        $flash->success(__('Rezervācija dzēsta.'));
    }

    public function render(): View
    {
        $bookings = Booking::query()
            ->with('product')
            ->orderBy('check_in')
            ->paginate(10);

        return view('livewire.admin.booking.booking-list', compact('bookings'))
            ->layout('layouts.admin.app');
    }
}
