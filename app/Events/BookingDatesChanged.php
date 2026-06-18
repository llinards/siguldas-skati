<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingDatesChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Booking $booking) {}
}
