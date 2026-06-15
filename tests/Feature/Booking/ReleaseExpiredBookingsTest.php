<?php

use App\Enums\BookingStatus;
use App\Models\Booking;

it('expires only pending holds past their deadline', function () {
    $expired = Booking::factory()->expired()->create();          // pending, expires_at in past
    $live = Booking::factory()->pending()->create();             // pending, expires_at in future
    $confirmed = Booking::factory()->create();                   // confirmed

    $this->artisan('bookings:release-expired')->assertSuccessful();

    expect($expired->fresh()->status)->toBe(BookingStatus::Expired)
        ->and($live->fresh()->status)->toBe(BookingStatus::Pending)
        ->and($confirmed->fresh()->status)->toBe(BookingStatus::Confirmed);
});
