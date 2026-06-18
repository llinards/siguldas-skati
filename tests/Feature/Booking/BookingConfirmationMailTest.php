<?php

use App\Enums\BookingStatus;
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

    // Exactly one of each — guards against the listener being registered twice
    // (auto-discovery + an explicit Event::listen), which doubled the emails.
    expect(Mail::queued(BookingConfirmedCustomerMail::class))->toHaveCount(1)
        ->and(Mail::queued(BookingConfirmedAdminMail::class))->toHaveCount(1);
});

it('renders the customer confirmation with reference and manage link', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-CONF1']);

    $rendered = (new BookingConfirmedCustomerMail($booking))->render();

    expect($rendered)->toContain('SS-CONF1')
        ->and($rendered)->toContain($booking->management_token);
});

it('includes the free-cancellation deadline while the booking is still cancellable', function () {
    Carbon\Carbon::setTestNow('2026-07-01');

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-20',
        'check_out' => '2026-07-23',
    ]);

    $rendered = (new BookingConfirmedCustomerMail($booking))->render();

    expect($rendered)->toContain('13.07.2026'); // 7 days before check-in

    Carbon\Carbon::setTestNow();
});
