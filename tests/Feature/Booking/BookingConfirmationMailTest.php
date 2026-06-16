<?php

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedAdminMail;
use App\Mail\BookingConfirmedCustomerMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => config()->set('booking.admin_email', 'ops@example.com'));

it('queues a customer and an admin email when a booking is confirmed', function () {
    Mail::fake();

    $booking = Booking::factory()->create(['guest_email' => 'guest@example.com']);

    event(new BookingConfirmed($booking));

    Mail::assertQueued(BookingConfirmedCustomerMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
    Mail::assertQueued(BookingConfirmedAdminMail::class, fn ($mail) => $mail->hasTo('ops@example.com'));
});

it('renders the customer confirmation with reference and manage link', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-CONF1']);

    $rendered = (new BookingConfirmedCustomerMail($booking))->render();

    expect($rendered)->toContain('SS-CONF1')
        ->and($rendered)->toContain($booking->management_token);
});
