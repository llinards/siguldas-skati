<?php

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledAdminMail;
use App\Mail\BookingCancelledCustomerMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => config()->set('booking.admin_email', 'ops@example.com'));

it('queues a customer and an admin email when a booking is cancelled', function () {
    Mail::fake();

    $booking = Booking::factory()->create([
        'guest_email' => 'guest@example.com',
        'refund_amount' => 54000,
    ]);

    event(new BookingCancelled($booking, refunded: true));

    Mail::assertQueued(BookingCancelledCustomerMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
    Mail::assertQueued(BookingCancelledAdminMail::class, fn ($mail) => $mail->hasTo('ops@example.com'));
});

it('shows the refunded amount in the customer cancellation email', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-CXL1', 'refund_amount' => 54000]);

    $rendered = (new BookingCancelledCustomerMail($booking, refunded: true))->render();

    expect($rendered)->toContain('SS-CXL1')->and($rendered)->toContain('540,00 €');
});
