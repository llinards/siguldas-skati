<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledAdminMail;
use App\Mail\BookingCancelledCustomerMail;
use Illuminate\Support\Facades\Mail;

class SendBookingCancellationNotifications
{
    public function handle(BookingCancelled $event): void
    {
        Mail::send(new BookingCancelledCustomerMail($event->booking, $event->refunded));
        Mail::send(new BookingCancelledAdminMail($event->booking, $event->refunded));
    }
}
