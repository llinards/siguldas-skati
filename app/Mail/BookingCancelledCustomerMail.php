<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $refunded = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->booking->guest_email,
            subject: __('Rezervācija atcelta').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.cancelled-customer',
            with: ['booking' => $this->booking, 'refunded' => $this->refunded],
        );
    }
}
