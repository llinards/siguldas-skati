<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Booking;

it('snapshots add-on name and price onto the booking pivot', function () {
    $booking = Booking::factory()->create();
    $addon = Addon::factory()->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'],
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay,
    ]);

    $booking->addons()->attach($addon->id, [
        'name' => 'Sauna',
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay->value,
        'quantity' => 1,
    ]);

    $pivot = $booking->fresh()->addons->first()->pivot;

    expect($booking->fresh()->addons)->toHaveCount(1)
        ->and($pivot->name)->toBe('Sauna')
        ->and((int) $pivot->price)->toBe(7000)
        ->and((int) $pivot->quantity)->toBe(1);
});
