<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: (string) config('booking.admin_email'),
            replyTo: $this->booking->guest_email,
            subject: __('Jauna rezervācija').' — '.$this->booking->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking.confirmed-admin',
            with: ['booking' => $this->booking->loadMissing('product')],
        );
    }
}
