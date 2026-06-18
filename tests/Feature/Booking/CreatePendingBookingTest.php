<?php

use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Product;
use App\Services\BookingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(BookingService::class);
    $this->product = Product::factory()->create([
        'base_price' => 10000,
        'min_nights' => 2,
        'person_count' => 4,
        'children_count' => 2,
    ]);
    $this->guest = ['name' => 'Jane Guest', 'email' => 'jane@example.com', 'phone' => '+37120000000'];
});

it('creates a pending booking with a hold, reference, token and totals', function () {
    $booking = $this->service->createPendingBooking(
        $this->product,
        Carbon::parse('2026-08-01'),
        Carbon::parse('2026-08-04'),
        2, 1, [], $this->guest,
    );

    expect($booking->status)->toBe(BookingStatus::Pending)
        ->and($booking->expires_at)->not->toBeNull()
        ->and($booking->expires_at->isFuture())->toBeTrue()
        ->and($booking->reference)->toStartWith('SS-')
        ->and($booking->management_token)->not->toBeEmpty()
        ->and($booking->nights_total)->toBe(30000) // 3 nights x 10000
        ->and($booking->grand_total)->toBe(30000) // nights only — no cleaning fee
        ->and($booking->guest_email)->toBe('jane@example.com');
});

it('records requested extras as flags without charging them', function () {
    $booking = $this->service->createPendingBooking(
        $this->product,
        Carbon::parse('2026-08-01'),
        Carbon::parse('2026-08-03'),
        2, 0,
        ['sauna_jacuzzi' => true, 'baby_cot' => false],
        $this->guest,
    );

    // The request is flagged for the admin to follow up, but the total stays nights-only.
    expect($booking->grand_total)->toBe(20000) // 2 nights x 10000
        ->and($booking->wants_sauna_jacuzzi)->toBeTrue()
        ->and($booking->wants_baby_cot)->toBeFalse();
});

it('rejects unavailable dates', function () {
    Booking::factory()->for($this->product)->create([
        'status' => BookingStatus::Confirmed, 'check_in' => '2026-08-01', 'check_out' => '2026-08-05',
    ]);

    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-02'), Carbon::parse('2026-08-04'), 2, 0, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects stays below the minimum nights', function () {
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-02'), 2, 0, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects more adults than the base spots', function () {
    // person_count (base/adult spots) = 4 → 5 adults is too many.
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 5, 0, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects more children than the dedicated child spots', function () {
    // children_count (dedicated child spots) = 2 → 3 children is too many.
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 1, 3, [], $this->guest,
    );
})->throws(BookingException::class);

it('allows adults plus dedicated child spots to add up', function () {
    // person_count = 4 adults + children_count = 2 children → 6 total valid.
    $booking = $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 4, 2, [], $this->guest,
    );

    expect($booking->adults)->toBe(4)->and($booking->children)->toBe(2);
});

it('lets children share the base spots when no child spots are set', function () {
    $house = Product::factory()->create([
        'base_price' => 10000, 'min_nights' => 2, 'person_count' => 4, 'children_count' => 0,
    ]);

    $booking = $this->service->createPendingBooking(
        $house, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 1, 3, [], $this->guest,
    );

    expect($booking->adults)->toBe(1)->and($booking->children)->toBe(3);
});

it('rejects guests beyond the base spots when children share them', function () {
    $house = Product::factory()->create([
        'base_price' => 10000, 'min_nights' => 2, 'person_count' => 4, 'children_count' => 0,
    ]);

    // 2 adults + 3 children = 5 > 4 shared base spots.
    $this->service->createPendingBooking(
        $house, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 2, 3, [], $this->guest,
    );
})->throws(BookingException::class);
