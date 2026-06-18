<?php

namespace App\Listeners;

use App\Events\BookingDatesChanged;
use App\Mail\BookingUpdatedCustomerMail;
use Illuminate\Support\Facades\Mail;

class SendBookingUpdatedNotification
{
    public function handle(BookingDatesChanged $event): void
    {
        Mail::send(new BookingUpdatedCustomerMail($event->booking));
    }
}
