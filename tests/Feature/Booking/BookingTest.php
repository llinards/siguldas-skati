<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Product;

it('persists a booking with casts and product relation', function () {
    $product = Product::factory()->create();

    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-01',
        'check_out' => '2026-07-05',
        'grand_total' => 72000,
    ]);

    expect($booking->product->is($product))->toBeTrue()
        ->and($booking->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->grand_total)->toBe(72000)
        ->and($booking->check_in->toDateString())->toBe('2026-07-01')
        ->and($booking->reference)->not->toBeEmpty()
        ->and($booking->management_token)->not->toBeEmpty();
});

it('defaults a generated reference and management token via factory', function () {
    $a = Booking::factory()->create();
    $b = Booking::factory()->create();

    expect($a->reference)->not->toBe($b->reference)
        ->and($a->management_token)->not->toBe($b->management_token);
});
