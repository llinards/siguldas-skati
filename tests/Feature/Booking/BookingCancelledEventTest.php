<?php

use App\Events\BookingCancelled;
use App\Models\Booking;

it('carries the booking and refunded flag', function () {
    $booking = Booking::factory()->create();

    $event = new BookingCancelled($booking, refunded: true);

    expect($event->booking->is($booking))->toBeTrue()
        ->and($event->refunded)->toBeTrue();
});
