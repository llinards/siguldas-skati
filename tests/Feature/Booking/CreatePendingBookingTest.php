<?php

use App\Enums\AddonPricingType;
use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Addon;
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

it('records selected add-ons as requests without charging them', function () {
    $sauna = Addon::factory()->for($this->product)->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'], 'price' => 7000, 'pricing_type' => AddonPricingType::PerStay,
    ]);

    $booking = $this->service->createPendingBooking(
        $this->product,
        Carbon::parse('2026-08-01'),
        Carbon::parse('2026-08-03'),
        2, 0,
        [['addon' => $sauna, 'quantity' => 1]],
        $this->guest,
    );

    // The add-on is attached for the admin to follow up, but the total stays nights-only.
    expect($booking->grand_total)->toBe(20000) // 2 nights x 10000
        ->and($booking->addons)->toHaveCount(1)
        ->and($booking->addons->first()->pivot->name)->toBe('Pirts'); // lv locale is active in tests
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

it('rejects more total guests than the house capacity', function () {
    // person_count (total capacity) = 4 → 3 adults + 2 children = 5 is too many.
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 3, 2, [], $this->guest,
    );
})->throws(BookingException::class);

it('rejects more children than the house child limit', function () {
    // children_count (max children) = 2 → 3 children is too many even within the total.
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 1, 3, [], $this->guest,
    );
})->throws(BookingException::class);

it('allows guests up to the total capacity', function () {
    // person_count = 4 total, children_count = 2 → 2 adults + 2 children = 4 valid.
    $booking = $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 2, 2, [], $this->guest,
    );

    expect($booking->adults)->toBe(2)->and($booking->children)->toBe(2);
});

it('allows children to fill the total when the house sets no child limit', function () {
    $house = Product::factory()->create([
        'base_price' => 10000, 'min_nights' => 2, 'person_count' => 4, 'children_count' => 0,
    ]);

    $booking = $this->service->createPendingBooking(
        $house, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 1, 3, [], $this->guest,
    );

    expect($booking->adults)->toBe(1)->and($booking->children)->toBe(3);
});
