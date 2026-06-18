<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function cancel(Booking $booking): View
    {
        return view('booking.cancel', compact('booking'));
    }

    public function manage(Booking $booking, string $token): View
    {
        // 404 (not 403) so a wrong token can't confirm the booking exists.
        abort_unless(hash_equals($booking->management_token, $token), 404);

        return view('booking.manage', compact('booking', 'token'));
    }
}
