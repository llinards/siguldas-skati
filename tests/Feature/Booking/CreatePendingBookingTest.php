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
        'cleaning_fee' => 3000,
        'min_nights' => 2,
        'person_count' => 4,
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
        ->and($booking->cleaning_fee)->toBe(3000)
        ->and($booking->grand_total)->toBe(33000)
        ->and($booking->guest_email)->toBe('jane@example.com');
});

it('snapshots selected add-ons onto the booking', function () {
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

    expect($booking->addons_total)->toBe(7000)
        ->and($booking->grand_total)->toBe(30000) // 2x10000 + 3000 + 7000
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

it('rejects guest counts above capacity', function () {
    $this->service->createPendingBooking(
        $this->product, Carbon::parse('2026-08-01'), Carbon::parse('2026-08-04'), 4, 2, [], $this->guest,
    );
})->throws(BookingException::class);
