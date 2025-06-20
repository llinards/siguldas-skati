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
        'is_active'   => true,
        'title'       => [
            'en' => 'Test Product',
            'lv' => 'Testa Produkts',
        ],
        'description' => [
            'en' => 'Test product description',
            'lv' => 'Testa produkta apraksts',
        ],
    ]);

    $productServices = new ProductServices();
    $activeProducts  = $productServices->getAllActiveProducts();

    expect($activeProducts)->toHaveCount(2);

    $activeProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
                        ->and($product->is_active)->toBeTruthy()
                        ->and($product->title)->toBeString()  // Should return localized title
                        ->and($product->slug)->toBeString()   // Should return localized slug
                        ->and($product->description)->toBeString(); // Should return localized description
    });
});

// NEW TESTS FOR MISSING FUNCTIONS

test('getAllProducts returns all products regardless of status', function () {
    // Create active products
    Product::factory()->count(3)->create(['is_active' => true]);

    // Create inactive products
    Product::factory()->count(2)->create(['is_active' => false]);

    $productServices = new ProductServices();
    $allProducts     = $productServices->getAllProducts();

    expect($allProducts)->toHaveCount(5);
});

test('getAllProducts returns empty collection when no products exist', function () {
    $productServices = new ProductServices();
    $allProducts     = $productServices->getAllProducts();

    expect($allProducts)->toBeEmpty();
});

test('getAllProducts returns products with description field', function () {
    Product::factory()->count(2)->create([
        'description' => [
            'en' => 'English description',
            'lv' => 'Latviešu apraksts',
        ],
    ]);

    $productServices = new ProductServices();
    $allProducts     = $productServices->getAllProducts();

    expect($allProducts)->toHaveCount(2);

    $allProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
                        ->and($product->description)->toBeString();
    });
});

test('deleteProduct successfully deletes existing product', function () {
    $product   = Product::factory()->create();
    $productId = $product->id;

    $productServices = new ProductServices();
    $result          = $productServices->deleteProduct($productId);

    expect($result)->toBeTrue()
                   ->and(Product::find($productId))->toBeNull();
});

test('deleteProduct returns false for non-existent product', function () {
    $productServices = new ProductServices();
    $result          = $productServices->deleteProduct(999);

    expect($result)->toBeFalse();
});

test('toggleProductStatus activates inactive product', function () {
    $product = Product::factory()->create(['is_active' => false]);

    $productServices = new ProductServices();
    $result          = $productServices->toggleProductStatus($product->id);

    expect($result)->toBeTrue()
                   ->and($product->fresh()->is_active)->toBeTrue();
});

test('toggleProductStatus deactivates active product', function () {
    $product = Product::factory()->create(['is_active' => true]);

    $productServices = new ProductServices();
    $result          = $productServices->toggleProductStatus($product->id);

    expect($result)->toBeTrue()
                   ->and($product->fresh()->is_active)->toBeFalse();
});

test('toggleProductStatus returns false for non-existent product', function () {
    $productServices = new ProductServices();
    $result          = $productServices->toggleProductStatus(999);

    expect($result)->toBeFalse();
});

test('getProduct returns existing product with description', function () {
    $product = Product::factory()->create([
        'title'       => [
            'en' => 'Test Product',
            'lv' => 'Testa Produkts',
        ],
        'description' => [
            'en' => 'Test product description',
            'lv' => 'Testa produkta apraksts',
        ],
    ]);

    $productServices = new ProductServices();
    $foundProduct    = $productServices->getProduct($product->id);

    expect($foundProduct)->toBeInstanceOf(Product::class)
                         ->and($foundProduct->id)->toBe($product->id)
                         ->and($foundProduct->title)->toBeString()
                         ->and($foundProduct->description)->toBeString();
});

test('getProduct returns null for non-existent product', function () {
    $productServices = new ProductServices();
    $foundProduct    = $productServices->getProduct(999);

    expect($foundProduct)->toBeNull();
});

test('getProduct returns product regardless of active status', function () {
    // Create inactive product
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    // Create active product
    $activeProduct = Product::factory()->create(['is_active' => true]);

    $productServices = new ProductServices();

    $foundInactive = $productServices->getProduct($inactiveProduct->id);
    $foundActive   = $productServices->getProduct($activeProduct->id);

    expect($foundInactive)->toBeInstanceOf(Product::class)
                          ->and($foundInactive->is_active)->toBeFalse()
                          ->and($foundInactive->description)->toBeString()
                          ->and($foundActive)->toBeInstanceOf(Product::class)
                          ->and($foundActive->is_active)->toBeTrue()
                          ->and($foundActive->description)->toBeString();
});
