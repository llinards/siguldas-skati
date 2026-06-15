<?php

use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Product;
use App\Services\AvailabilityService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(AvailabilityService::class);
    $this->product = Product::factory()->create();
});

it('is available when nothing overlaps', function () {
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeTrue();
});

it('is unavailable when a confirmed booking overlaps', function () {
    Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-03', 'check_out' => '2026-07-06',
    ]);

    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeFalse();
});

it('allows a back-to-back stay starting on a checkout day', function () {
    Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);

    // new stay starts exactly when the previous checks out
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-04'), Carbon::parse('2026-07-06')))
        ->toBeTrue();
});

it('ignores expired pending holds but blocks live ones', function () {
    Booking::factory()->for($this->product)->expired()->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeTrue();

    Booking::factory()->for($this->product)->pending()->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04')))
        ->toBeFalse();
});

it('is unavailable when a blocked range overlaps (inclusive end)', function () {
    BlockedDate::factory()->for($this->product)->create([
        'start_date' => '2026-07-05', 'end_date' => '2026-07-05',
    ]);

    // stay 07-04 -> 07-06 covers the night of 07-05, which is blocked
    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-04'), Carbon::parse('2026-07-06')))
        ->toBeFalse();
});

it('can ignore a given booking id', function () {
    $booking = Booking::factory()->for($this->product)->create([
        'check_in' => '2026-07-01', 'check_out' => '2026-07-04',
    ]);

    expect($this->service->isAvailable($this->product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'), $booking->id))
        ->toBeTrue();
});
