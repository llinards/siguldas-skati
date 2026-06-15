<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function success(Booking $booking): View
    {
        return view('booking.success', compact('booking'));
    }

    public function cancel(Booking $booking): View
    {
        return view('booking.cancel', compact('booking'));
    }
}
