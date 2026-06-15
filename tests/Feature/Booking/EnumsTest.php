<?php

use App\Enums\AddonPricingType;
use App\Enums\BookingStatus;

it('exposes the expected booking statuses', function () {
    expect(BookingStatus::Pending->value)->toBe('pending')
        ->and(BookingStatus::Confirmed->value)->toBe('confirmed')
        ->and(BookingStatus::Expired->value)->toBe('expired')
        ->and(BookingStatus::Cancelled->value)->toBe('cancelled');
});

it('exposes the expected add-on pricing types', function () {
    expect(AddonPricingType::PerStay->value)->toBe('per_stay')
        ->and(AddonPricingType::PerNight->value)->toBe('per_night');
});
