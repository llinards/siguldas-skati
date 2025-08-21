<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phoneNumber,
        public string $email,
        public string $question,
        public string $ipAddress
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: 'info@siguldasskati.lv',
            //            to: 'siguldasskati@gmail.com',
            replyTo: $this->email,
            subject: 'Jauns kontaktu ziņojums no mājas lapas',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-us-mail',
            with: [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'phoneNumber' => $this->phoneNumber,
                'email' => $this->email,
                'question' => $this->question,
                'ipAddress' => $this->ipAddress,
            ]
        );
    }
}
