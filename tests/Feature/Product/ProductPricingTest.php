<?php

use App\Livewire\Admin\Product\ProductPricing;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product and shows current base price in euros', function () {
    $product = Product::factory()->create(['base_price' => 15000, 'min_nights' => 2]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id)
        ->assertSet('basePrice', 150.0)
        ->assertSet('minNights', 2);
});

it('saves the base price (euros) as cents and the minimum nights', function () {
    $product = Product::factory()->create(['base_price' => 0, 'min_nights' => 1]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('basePrice', 149.50)
        ->set('minNights', 3)
        ->call('saveBaseSettings')
        ->assertHasNoErrors();

    $product->refresh();
    expect($product->base_price)->toBe(14950)
        ->and($product->min_nights)->toBe(3);
});

it('rejects a base price below zero and minimum nights below one', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('basePrice', -5)
        ->set('minNights', 0)
        ->call('saveBaseSettings')
        ->assertHasErrors(['basePrice', 'minNights']);
});

it('applies an override price (euros) to each selected date as cents', function () {
    $product = Product::factory()->create(['base_price' => 15000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', ['2026-07-12', '2026-07-13'])
        ->set('overridePrice', 180)
        ->call('applyPriceToSelected')
        ->assertHasNoErrors()
        ->assertSet('selectedDates', [])
        ->assertSet('overridePrice', null);

    expect(ProductPrice::where('product_id', $product->id)->count())->toBe(2)
        ->and(ProductPrice::where('product_id', $product->id)->where('date', '2026-07-12')->value('price'))->toBe(18000);
});

it('overwrites the price for a date that already has an override', function () {
    $product = Product::factory()->create();
    ProductPrice::create(['product_id' => $product->id, 'date' => '2026-07-12', 'price' => 18000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', ['2026-07-12'])
        ->set('overridePrice', 200)
        ->call('applyPriceToSelected')
        ->assertHasNoErrors();

    expect(ProductPrice::where('product_id', $product->id)->count())->toBe(1)
        ->and(ProductPrice::where('product_id', $product->id)->where('date', '2026-07-12')->value('price'))->toBe(20000);
});

it('rejects applying with no dates selected or a non-positive price', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->set('selectedDates', [])
        ->set('overridePrice', 0)
        ->call('applyPriceToSelected')
        ->assertHasErrors(['selectedDates', 'overridePrice']);
});

it('removes a single date override', function () {
    $product = Product::factory()->create();
    $override = ProductPrice::create(['product_id' => $product->id, 'date' => '2026-07-12', 'price' => 18000]);

    Livewire::test(ProductPricing::class, ['product' => $product->id])
        ->call('removeOverride', $override->id)
        ->assertHasNoErrors();

    expect(ProductPrice::find($override->id))->toBeNull();
});
