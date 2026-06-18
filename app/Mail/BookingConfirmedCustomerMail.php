<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->booking->guest_email,
            subject: __('Rezervācija apstiprināta').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.confirmed-customer',
            with: ['booking' => $this->booking->loadMissing('product')],
        );
    }
}
