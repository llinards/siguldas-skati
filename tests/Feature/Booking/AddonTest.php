<?php

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;

it('belongs to a product, is translatable, and casts pricing type', function () {
    $product = Product::factory()->create();

    $addon = Addon::factory()->for($product)->create([
        'name' => ['lv' => 'Pirts', 'en' => 'Sauna'],
        'price' => 7000,
        'pricing_type' => AddonPricingType::PerStay,
    ]);

    expect($addon->product->is($product))->toBeTrue()
        ->and($addon->pricing_type)->toBe(AddonPricingType::PerStay)
        ->and($addon->getTranslation('name', 'en'))->toBe('Sauna')
        ->and($product->fresh()->addons)->toHaveCount(1);
});
