<?php

use App\Enums\BookingStatus;

it('exposes the expected booking statuses', function () {
    expect(BookingStatus::Pending->value)->toBe('pending')
        ->and(BookingStatus::Confirmed->value)->toBe('confirmed')
        ->and(BookingStatus::Expired->value)->toBe('expired')
        ->and(BookingStatus::Cancelled->value)->toBe('cancelled');
});
