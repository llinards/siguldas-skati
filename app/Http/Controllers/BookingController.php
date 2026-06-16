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

    public function manage(Booking $booking, string $token): View
    {
        abort_unless(hash_equals($booking->management_token, $token), 403);

        return view('booking.manage', compact('booking', 'token'));
    }
}
