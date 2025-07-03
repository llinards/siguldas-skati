<?php

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->productServices = new ProductServices();
});

test('getAllActiveProducts returns only active products with proper structure', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $activeProducts = $this->productServices->getAllActiveProducts();

    expect($activeProducts)->toHaveCount(3)
                           ->and($activeProducts->every(fn($product) => (bool) $product->is_active))->toBeTrue();

    // Verify product structure
    $activeProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
                        ->and($product->title)->toBeString()
                        ->and($product->slug)->toBeString()
                        ->and($product->description)->toBeString();
    });
});

test('getAllOtherProducts excludes specified product and returns only active products', function () {
    $products = Product::factory()->count(5)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $excludedProduct = $products->first();
    $otherProducts   = $this->productServices->getAllOtherProducts($excludedProduct);

    expect($otherProducts)->toHaveCount(4)
                          ->and($otherProducts->contains('id', $excludedProduct->id))->toBeFalse()
                          ->and($otherProducts->every(fn($product) => (bool) $product->is_active))->toBeTrue();
});

test('getAllProducts returns all products regardless of status', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $allProducts = $this->productServices->getAllProducts();

    expect($allProducts)->toHaveCount(5);

    // Verify each product has proper structure
    $allProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
                        ->and($product->description)->toBeString();
    });
});

test('returns empty collections when no products match criteria', function () {
    // Test with no products at all
    expect($this->productServices->getAllActiveProducts())->toBeEmpty()
                                                          ->and($this->productServices->getAllProducts())->toBeEmpty();

    // Test with only inactive products
    Product::factory()->count(3)->create(['is_active' => false]);
    expect($this->productServices->getAllActiveProducts())->toBeEmpty();

    // Test with single product and no others
    $singleProduct = Product::factory()->create(['is_active' => true]);
    expect($this->productServices->getAllOtherProducts($singleProduct))->toBeEmpty();
});

test('getProductById returns existing product with proper structure regardless of status', function () {
    $activeProduct   = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    $foundActive   = $this->productServices->getProductById($activeProduct->id);
    $foundInactive = $this->productServices->getProductById($inactiveProduct->id);

    expect($foundActive)->toBeInstanceOf(Product::class)
                        ->and($foundActive->id)->toBe($activeProduct->id)
                        ->and($foundActive->is_active)->toBeTrue()
                        ->and($foundActive->description)->toBeString()
                        ->and($foundInactive)->toBeInstanceOf(Product::class)
                        ->and($foundInactive->id)->toBe($inactiveProduct->id)
                        ->and($foundInactive->is_active)->toBeFalse()
                        ->and($foundInactive->description)->toBeString();
});

test('getProductById returns null for non-existent product', function () {
    expect($this->productServices->getProductById(999))->toBeNull();
});

test('deleteProduct successfully removes existing product from database', function () {
    $product = Product::factory()->create();

    $result = $this->productServices->deleteProduct($product);

    expect($result)->toBeTrue()
                   ->and(Product::find($product->id))->toBeNull();
});

test('toggleProductStatus switches between active and inactive states', function () {
    $activeProduct   = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    // Test deactivating active product
    $result1 = $this->productServices->toggleProductStatus($activeProduct->id);
    expect($result1)->toBeTrue()
                    ->and($activeProduct->fresh()->is_active)->toBeFalse();

    // Test activating inactive product
    $result2 = $this->productServices->toggleProductStatus($inactiveProduct->id);
    expect($result2)->toBeTrue()
                    ->and($inactiveProduct->fresh()->is_active)->toBeTrue();
});

test('toggleProductStatus returns false for non-existent product', function () {
    expect($this->productServices->toggleProductStatus(999))->toBeFalse();
});

test('generateSlug creates proper URL-friendly slugs from various title formats', function () {
    $testCases = [
        'Test Product Title'                       => 'test-product-title',
        'Test Product! With @Special# Characters$' => 'test-product-with-at-special-characters',
        'Produkts ar latviešu simboliem āēīōū'     => 'produkts-ar-latviesu-simboliem-aeiou',
        'Multiple   Spaces    Between Words'       => 'multiple-spaces-between-words',
        'UPPERCASE TITLE'                          => 'uppercase-title',
        'Mixed CaSe TiTlE'                         => 'mixed-case-title',
    ];

    foreach ($testCases as $input => $expected) {
        expect($this->productServices->generateSlug($input))->toBe($expected);
    }
});
