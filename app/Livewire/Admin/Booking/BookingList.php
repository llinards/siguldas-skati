<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public function render(): View
    {
        $bookings = Booking::query()
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('livewire.admin.booking.booking-list', compact('bookings'))
            ->layout('layouts.admin.app');
    }
}
