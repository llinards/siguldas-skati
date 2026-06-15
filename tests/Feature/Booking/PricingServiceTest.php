<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\PricingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = app(PricingService::class);
});

it('uses base_price for nights without an override', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'cleaning_fee' => 0]);

    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    // 3 nights x 15000
    expect($quote->nights)->toBe(3)
        ->and($quote->nightsTotal)->toBe(45000)
        ->and($quote->grandTotal)->toBe(45000);
});

it('uses per-date overrides where present and base_price elsewhere', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'cleaning_fee' => 0]);
    ProductPrice::factory()->for($product)->create(['date' => '2026-07-02', 'price' => 20000]);

    // nights: 07-01 (15000) + 07-02 (20000) + 07-03 (15000) = 50000
    $quote = $this->service->quote($product, Carbon::parse('2026-07-01'), Carbon::parse('2026-07-04'));

    expect($quote->nightsTotal)->toBe(50000)
        ->and($quote->grandTotal)->toBe(50000);
});

it('adds the cleaning fee and add-ons (per_stay and per_night)', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'cleaning_fee' => 3000]);
    $sauna = Addon::factory()->for($product)->create(['price' => 7000, 'pricing_type' => AddonPricingType::PerStay]);
    $crib = Addon::factory()->for($product)->create(['price' => 500, 'pricing_type' => AddonPricingType::PerNight]);

    // 2 nights x 10000 = 20000; cleaning 3000; sauna 7000; crib 500 x 2 = 1000 => 31000
    $quote = $this->service->quote(
        $product,
        Carbon::parse('2026-07-01'),
        Carbon::parse('2026-07-03'),
        [['addon' => $sauna, 'quantity' => 1], ['addon' => $crib, 'quantity' => 1]],
    );

    expect($quote->nightsTotal)->toBe(20000)
        ->and($quote->cleaningFee)->toBe(3000)
        ->and($quote->addonsTotal)->toBe(8000)
        ->and($quote->grandTotal)->toBe(31000);
});

it('throws when check_out is not after check_in', function () {
    $product = Product::factory()->create(['base_price' => 10000]);

    expect(fn () => $this->service->quote($product, Carbon::parse('2026-07-05'), Carbon::parse('2026-07-05')))
        ->toThrow(InvalidArgumentException::class);
});
