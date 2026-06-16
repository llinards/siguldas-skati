<?php

use App\Models\Product;

it('hides the reservation section when the house has no price set', function () {
    $product = Product::factory()->create(['is_active' => true, 'base_price' => 0]);

    $this->get(route('product', $product))
        ->assertOk()
        ->assertDontSee(__('Rezervācija'));
});

it('shows the reservation section when the house has a price', function () {
    $product = Product::factory()->create(['is_active' => true, 'base_price' => 15000]);

    $this->get(route('product', $product))
        ->assertOk()
        ->assertSee(__('Rezervācija'));
});
