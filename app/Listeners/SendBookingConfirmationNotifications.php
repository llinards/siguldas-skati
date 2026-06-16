<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedAdminMail;
use App\Mail\BookingConfirmedCustomerMail;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationNotifications
{
    public function handle(BookingConfirmed $event): void
    {
        Mail::send(new BookingConfirmedCustomerMail($event->booking));
        Mail::send(new BookingConfirmedAdminMail($event->booking));
    }
}
