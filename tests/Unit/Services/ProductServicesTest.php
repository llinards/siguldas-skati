<?php

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('getAllActiveProducts returns only active products', function () {
    // Create active products
    Product::factory()->count(3)->create(['is_active' => true]);

    // Create inactive products
    Product::factory()->count(2)->create(['is_active' => false]);

    $productServices = new ProductServices();
    $activeProducts  = $productServices->getAllActiveProducts();

    expect($activeProducts)->toHaveCount(3)
                           ->and($activeProducts->every(fn($product) => (bool) $product->is_active))->toBeTrue();
});

test('getAllOtherProducts excludes the specified product', function () {
    // Create active products
    $products = Product::factory()->count(5)->create(['is_active' => true]);

    // Create inactive products (these should be excluded)
    Product::factory()->count(2)->create(['is_active' => false]);

    // Select one product to exclude
    $excludedProduct = $products->first();

    $productServices = new ProductServices();
    $otherProducts   = $productServices->getAllOtherProducts($excludedProduct);

    // Should return 4 products (5 active minus 1 excluded)
    expect($otherProducts)->toHaveCount(4)
                          ->and($otherProducts->contains('id', $excludedProduct->id))->toBeFalse()
                          ->and($otherProducts->every(fn($product) => (bool) $product->is_active))->toBeTrue();
});

test('getAllActiveProducts returns empty collection when no active products exist', function () {
    // Create only inactive products
    Product::factory()->count(3)->create(['is_active' => false]);

    $productServices = new ProductServices();
    $activeProducts  = $productServices->getAllActiveProducts();

    expect($activeProducts)->toBeEmpty();
});

test('getAllOtherProducts returns empty collection when no other active products exist', function () {
    // Create a single active product
    $product = Product::factory()->create(['is_active' => true]);

    // Create inactive products (these should be excluded)
    Product::factory()->count(2)->create(['is_active' => false]);

    $productServices = new ProductServices();
    $otherProducts   = $productServices->getAllOtherProducts($product);

    expect($otherProducts)->toBeEmpty();
});

test('getAllActiveProducts returns products with proper structure', function () {
    // Create active products with specific data
    Product::factory()->count(2)->create([
        'is_active' => true,
        'title'     => [
            'en' => 'Test Product',
            'lv' => 'Testa Produkts',
        ],
    ]);

    $productServices = new ProductServices();
    $activeProducts  = $productServices->getAllActiveProducts();

    expect($activeProducts)->toHaveCount(2);

    $activeProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
                        ->and($product->is_active)->toBeTruthy()
                        ->and($product->title)->toBeString()  // Should return localized title
                        ->and($product->slug)->toBeString();  // Should return localized slug
    });
});
