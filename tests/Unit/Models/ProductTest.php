<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('factory creates valid model instance with all required attributes', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class)
                    ->and($product->getTranslations('title'))->toBeArray()
                    ->and($product->getTranslations('slug'))->toBeArray()
                    ->and($product->getTranslations('description'))->toBeArray()
                    ->and($product->is_active)->toBeTrue()
                    ->and($product->person_count)->toBeInt()
                    ->and($product->cover)->toBeString();
});

test('uses slug as route key name for URL generation', function () {
    $product = new Product();
    expect($product->getRouteKeyName())->toBe('slug');
});

test('translatable attributes work correctly across different locales', function () {
    $product = Product::factory()->create([
        'title'       => ['en' => 'English Product', 'lv' => 'Latviešu Produkts'],
        'slug'        => ['en' => 'english-product', 'lv' => 'latviesu-produkts'],
        'description' => ['en' => 'English description', 'lv' => 'Latviešu apraksts'],
    ]);

    // Test English locale
    app()->setLocale('en');
    expect($product->title)->toBe('English Product')
                           ->and($product->slug)->toBe('english-product')
                           ->and($product->description)->toBe('English description');

    // Test Latvian locale
    app()->setLocale('lv');
    expect($product->title)->toBe('Latviešu Produkts')
                           ->and($product->slug)->toBe('latviesu-produkts')
                           ->and($product->description)->toBe('Latviešu apraksts');
});

test('falls back to Latvian when English translation is missing, null, or empty', function () {
    $productMissing = Product::factory()->create([
        'title'       => ['lv' => 'Latviešu Nosaukums'],
        'slug'        => ['lv' => 'latviesu-nosaukums'],
        'description' => ['lv' => 'Latviešu Apraksts'],
    ]);

    $productNullEmpty = Product::factory()->create([
        'title'       => ['lv' => 'Cits Nosaukums', 'en' => null],
        'slug'        => ['lv' => 'cits-nosaukums', 'en' => ''],
        'description' => ['lv' => 'Cits Apraksts', 'en' => null],
    ]);

    app()->setLocale('en');

    // Test missing translations
    expect($productMissing->title)->toBe('Latviešu Nosaukums')
                                  ->and($productMissing->slug)->toBe('latviesu-nosaukums')
                                  ->and($productMissing->description)->toBe('Latviešu Apraksts');

    // Test null/empty translations
    expect($productNullEmpty->title)->toBe('Cits Nosaukums')
                                    ->and($productNullEmpty->slug)->toBe('cits-nosaukums')
                                    ->and($productNullEmpty->description)->toBe('Cits Apraksts');
});

test('resolves route binding by slug in current locale for active products only', function () {
    $activeProduct = Product::factory()->create([
        'slug'      => ['en' => 'active-product', 'lv' => 'aktivs-produkts'],
        'is_active' => true,
    ]);

    $inactiveProduct = Product::factory()->create([
        'slug'      => ['en' => 'inactive-product', 'lv' => 'neaktivs-produkts'],
        'is_active' => false,
    ]);

    // Test English locale
    app()->setLocale('en');
    expect((new Product())->resolveRouteBinding('active-product'))
        ->toBeInstanceOf(Product::class)
        ->and((new Product())->resolveRouteBinding('active-product')->id)
        ->toBe($activeProduct->id)
        ->and((new Product())->resolveRouteBinding('inactive-product'))
        ->toBeNull();

    // Test Latvian locale
    app()->setLocale('lv');
    expect((new Product())->resolveRouteBinding('aktivs-produkts'))
        ->toBeInstanceOf(Product::class)
        ->and((new Product())->resolveRouteBinding('aktivs-produkts')->id)
        ->toBe($activeProduct->id)
        ->and((new Product())->resolveRouteBinding('neaktivs-produkts'))
        ->toBeNull();
});

test('description attribute handles various content types correctly', function () {
    $productWithContent = Product::factory()->create([
        'description' => ['en' => 'Rich description', 'lv' => 'Bagāts apraksts'],
    ]);

    $productEmpty = Product::factory()->create([
        'description' => ['en' => '', 'lv' => ''],
    ]);

    // Test with content
    app()->setLocale('en');
    expect($productWithContent->description)->toBeString()
                                            ->and($productWithContent->description)->toBe('Rich description');

    app()->setLocale('lv');
    expect($productWithContent->description)->toBeString()
                                            ->and($productWithContent->description)->toBe('Bagāts apraksts');

    // Test empty descriptions
    app()->setLocale('en');
    expect($productEmpty->description)->toBe('');

    app()->setLocale('lv');
    expect($productEmpty->description)->toBe('');
});

test('global scope orders products by order column automatically', function () {
    $product1 = Product::factory()->create(['order' => 3]);
    $product2 = Product::factory()->create(['order' => 1]);
    $product3 = Product::factory()->create(['order' => 2]);

    $products = Product::all();

    expect($products->pluck('id')->toArray())
        ->toBe([$product2->id, $product3->id, $product1->id]);
});

test('resolveRouteBinding finds product by any locale slug', function () {
    $product = Product::factory()->create([
        'slug'      => ['lv' => 'test-lv', 'en' => 'test-en'],
        'is_active' => true,
    ]);

    expect($product->resolveRouteBinding('test-lv'))->not->toBeNull();
    expect($product->resolveRouteBinding('test-en'))->not->toBeNull();
});
