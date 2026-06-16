<?php

use App\Livewire\Admin\Product\ProductPricing;
use App\Models\Product;
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
