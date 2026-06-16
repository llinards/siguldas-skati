<?php

use App\Models\Booking;
use App\Services\StripeService;

it('builds a single nights line item matching the grand total', function () {
    $booking = Booking::factory()->create([
        'nights_total' => 30000, 'grand_total' => 30000,
    ]);

    $items = app(StripeService::class)->buildLineItems($booking->fresh());

    expect($items)->toHaveCount(1)
        ->and($items[0]['price_data']['unit_amount'])->toBe(30000)
        ->and($items[0]['price_data']['currency'])->toBe('eur')
        ->and($items[0]['quantity'])->toBe(1);
});
