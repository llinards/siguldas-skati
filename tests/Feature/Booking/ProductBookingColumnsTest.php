<?php

use App\Models\Product;

it('has booking pricing columns with sensible defaults', function () {
    $product = Product::factory()->create();

    expect($product->base_price)->toBe(0)
        ->and($product->cleaning_fee)->toBe(0)
        ->and($product->min_nights)->toBe(1);
});
