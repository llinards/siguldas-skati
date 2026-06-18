<?php

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\PricingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(PricingService::class);
});

it('uses base_price for nights without an override', function () {
    $product = Product::factory()->create(['base_price' => 15000]);

    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    // 3 nights x 15000
    expect($quote->nights)->toBe(3)
        ->and($quote->nightsTotal)->toBe(45000)
        ->and($quote->grandTotal)->toBe(45000)
        ->and($quote->nightlyRates)->toBe([15000, 15000, 15000])
        ->and($quote->hasUniformRate())->toBeTrue()
        ->and($quote->uniformRate())->toBe(15000);
});

it('uses per-date overrides where present and base_price elsewhere', function () {
    $product = Product::factory()->create(['base_price' => 15000]);
    ProductPrice::factory()->for($product)->create(['date' => '2026-07-02', 'price' => 20000]);

    // nights: 07-01 (15000) + 07-02 (20000) + 07-03 (15000) = 50000
    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    expect($quote->nightsTotal)->toBe(50000)
        ->and($quote->grandTotal)->toBe(50000)
        ->and($quote->nightlyRates)->toBe([15000, 20000, 15000])
        ->and($quote->hasUniformRate())->toBeFalse()  // mixed nights → no single "€X × N" rate
        ->and($quote->uniformRate())->toBeNull();
});

it('charges only the nights total (no cleaning fee or add-ons)', function () {
    $product = Product::factory()->create(['base_price' => 10000]);

    // 2 nights x 10000 = 20000
    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-03'));

    expect($quote->nightsTotal)->toBe(20000)
        ->and($quote->grandTotal)->toBe(20000);
});

it('throws when check_out is not after check_in', function () {
    $product = Product::factory()->create(['base_price' => 10000]);

    expect(fn () => $this->service->quote($product, Carbon::parse('2026-07-05'), Carbon::parse('2026-07-05')))
        ->toThrow(InvalidArgumentException::class);
});
