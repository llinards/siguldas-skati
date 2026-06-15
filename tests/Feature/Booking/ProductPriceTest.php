<?php

use App\Models\Product;
use App\Models\ProductPrice;

it('belongs to a product and stores a per-date price in cents', function () {
    $product = Product::factory()->create();

    $price = ProductPrice::factory()->for($product)->create([
        'date' => '2026-07-01',
        'price' => 18000,
    ]);

    expect($price->product->is($product))->toBeTrue()
        ->and($price->price)->toBe(18000)
        ->and($product->fresh()->prices)->toHaveCount(1);
});
