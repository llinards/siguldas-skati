<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('product factory creates valid product', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class)
                    ->and($product->getTranslations('title'))->toBeArray()  // Get the full translations array
                    ->and($product->getTranslations('slug'))->toBeArray()   // Get the full translations array
                    ->and($product->getTranslations('description'))->toBeArray()   // Get the full translations array
                    ->and($product->is_active)->toBeTrue()
                    ->and($product->person_count)->toBeInt()
                    ->and($product->cover)->toBeString();
});

test('product has translatable title, slug and description', function () {
    $product = Product::factory()->create([
        'title'       => [
            'en' => 'Test Product',
            'lv' => 'Testa Produkts',
        ],
        'slug'        => [
            'en' => 'test-product',
            'lv' => 'testa-produkts',
        ],
        'description' => [
            'en' => 'Test product description in English',
            'lv' => 'Testa produkta apraksts latviešu valodā',
        ],
    ]);

    // Test English locale
    app()->setLocale('en');
    expect($product->title)->toBe('Test Product')
                           ->and($product->slug)->toBe('test-product')
                           ->and($product->description)->toBe('Test product description in English');

    // Test Latvian locale
    app()->setLocale('lv');
    expect($product->title)->toBe('Testa Produkts')
                           ->and($product->slug)->toBe('testa-produkts')
                           ->and($product->description)->toBe('Testa produkta apraksts latviešu valodā');
});

test('product route key name is slug', function () {
    $product = new Product();

    expect($product->getRouteKeyName())->toBe('slug');
});

test('product resolves route binding by slug in current locale', function () {
    // Create an active product
    $activeProduct = Product::factory()->create([
        'slug'      => [
            'en' => 'active-product',
            'lv' => 'aktivs-produkts',
        ],
        'is_active' => true,
    ]);

    // Create an inactive product
    $inactiveProduct = Product::factory()->create([
        'slug'      => [
            'en' => 'inactive-product',
            'lv' => 'neaktivs-produkts',
        ],
        'is_active' => false,
    ]);

    // Test English locale
    app()->setLocale('en');
    $resolvedActive   = (new Product())->resolveRouteBinding('active-product');
    $resolvedInactive = (new Product())->resolveRouteBinding('inactive-product');

    expect($resolvedActive->id)->toBe($activeProduct->id)
                               ->and($resolvedInactive)->toBeNull();

    // Test Latvian locale
    app()->setLocale('lv');
    $resolvedActive   = (new Product())->resolveRouteBinding('aktivs-produkts');
    $resolvedInactive = (new Product())->resolveRouteBinding('neaktivs-produkts');

    expect($resolvedActive->id)->toBe($activeProduct->id)
                               ->and($resolvedInactive)->toBeNull();
});

test('product falls back to LV when EN translation is missing', function () {
    $product = Product::factory()->create([
        'title'       => [
            'lv' => 'Latviešu nosaukums',
            // No 'en' translation provided
        ],
        'slug'        => [
            'lv' => 'latviesu-nosaukums',
            // No 'en' translation provided
        ],
        'description' => [
            'lv' => 'Latviešu apraksts',
            // No 'en' translation provided
        ],
    ]);

    // Set locale to English (which has missing translations)
    app()->setLocale('en');

    // Should fallback to LV
    expect($product->title)->toBe('Latviešu nosaukums')
                           ->and($product->slug)->toBe('latviesu-nosaukums')
                           ->and($product->description)->toBe('Latviešu apraksts');
});

test('product falls back to LV when EN translation is null or empty', function () {
    $product = Product::factory()->create([
        'title'       => [
            'lv' => 'Latviešu nosaukums',
            'en' => null, // Explicitly null
        ],
        'slug'        => [
            'lv' => 'latviesu-nosaukums',
            'en' => '', // Empty string
        ],
        'description' => [
            'lv' => 'Latviešu apraksts',
            'en' => null, // Explicitly null
        ],
    ]);

    // Set locale to English
    app()->setLocale('en');

    // Should fallback to LV
    expect($product->title)->toBe('Latviešu nosaukums')
                           ->and($product->slug)->toBe('latviesu-nosaukums')
                           ->and($product->description)->toBe('Latviešu apraksts');
});

test('product uses EN translation when available', function () {
    $product = Product::factory()->create([
        'title'       => [
            'lv' => 'Latviešu nosaukums',
            'en' => 'English Title',
        ],
        'slug'        => [
            'lv' => 'latviesu-nosaukums',
            'en' => 'english-title',
        ],
        'description' => [
            'lv' => 'Latviešu apraksts',
            'en' => 'English description',
        ],
    ]);

    // Set locale to English
    app()->setLocale('en');

    // Should use EN translation
    expect($product->title)->toBe('English Title')
                           ->and($product->slug)->toBe('english-title')
                           ->and($product->description)->toBe('English description');

    // Set locale to Latvian
    app()->setLocale('lv');

    // Should use LV translation
    expect($product->title)->toBe('Latviešu nosaukums')
                           ->and($product->slug)->toBe('latviesu-nosaukums')
                           ->and($product->description)->toBe('Latviešu apraksts');
});

test('product description is accessible as string', function () {
    $product = Product::factory()->create([
        'description' => [
            'en' => 'English description',
            'lv' => 'Latviešu apraksts',
        ],
    ]);

    app()->setLocale('en');
    expect($product->description)->toBeString()
                                 ->and($product->description)->toBe('English description');

    app()->setLocale('lv');
    expect($product->description)->toBeString()
                                 ->and($product->description)->toBe('Latviešu apraksts');
});

test('product description can be empty', function () {
    $product = Product::factory()->create([
        'description' => [
            'en' => '',
            'lv' => '',
        ],
    ]);

    app()->setLocale('en');
    expect($product->description)->toBe('');

    app()->setLocale('lv');
    expect($product->description)->toBe('');
});
