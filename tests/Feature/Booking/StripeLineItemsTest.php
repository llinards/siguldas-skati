<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Booking;
use App\Services\StripeService;

it('builds one line item per cost component summing to grand_total', function () {
    $booking = Booking::factory()->create([
        'nights_total' => 30000, 'cleaning_fee' => 3000, 'addons_total' => 7000, 'grand_total' => 40000,
    ]);
    $addon = Addon::factory()->create(['pricing_type' => AddonPricingType::PerStay]);
    $booking->addons()->attach($addon->id, [
        'name' => 'Sauna', 'price' => 7000, 'pricing_type' => 'per_stay', 'quantity' => 1,
    ]);

    $items = app(StripeService::class)->buildLineItems($booking->fresh());

    $sum = collect($items)->sum(fn ($i) => $i['price_data']['unit_amount'] * $i['quantity']);

    expect($sum)->toBe(40000)
        ->and($items)->toHaveCount(3) // stay + cleaning + sauna
        ->and($items[0]['price_data']['currency'])->toBe('eur');
});

it('omits the cleaning fee line when it is zero', function () {
    $booking = Booking::factory()->create([
        'nights_total' => 20000, 'cleaning_fee' => 0, 'addons_total' => 0, 'grand_total' => 20000,
    ]);

    $items = app(StripeService::class)->buildLineItems($booking->fresh());

    expect($items)->toHaveCount(1)
        ->and($items[0]['price_data']['unit_amount'])->toBe(20000);
});
