<?php

use App\Livewire\Admin\Product\EditProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully when mounted with existing product', function () {
    $product = Product::factory()->create([
        'title'       => ['en' => 'Test Product', 'lv' => 'Testa Produkts'],
        'description' => ['en' => 'Description', 'lv' => 'Apraksts'],
        'is_active'   => true,
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->assertStatus(200)
            ->assertSet('title', 'Testa Produkts')
            ->assertSet('description', 'Apraksts')
            ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    $product = Product::factory()->create();

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->set('title', '')
            ->set('description', '')
            ->call('update')
            ->assertHasErrors(['title', 'description']);
});

test('successfully updates product with new data, generates slug, and shows success message', function () {
    $product = Product::factory()->create([
        'title'       => ['en' => 'Old Title', 'lv' => 'Vecais Nosaukums'],
        'description' => ['en' => 'Old Description', 'lv' => 'Vecais Apraksts'],
        'slug'        => ['en' => 'old-title', 'lv' => 'vecais-nosaukums'],
        'is_active'   => false,
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->set('title', 'Jaunais Nosaukums')
            ->set('description', 'Jaunais Apraksts')
            ->set('is_active', true)
            ->call('update')
            ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();
    expect($product->title)->toBe('Jaunais Nosaukums')
                           ->and($product->description)->toBe('Jaunais Apraksts')
                           ->and($product->is_active)->toBeTrue()
                           ->and($product->slug)->toBe('jaunais-nosaukums');
});

test('generates proper URL slugs when updating titles across different locales', function () {
    // Test Latvian locale with special characters
    app()->setLocale('lv');
    $product = Product::factory()->create([
        'title'       => ['lv' => 'Vecais Nosaukums'],
        'description' => ['lv' => 'Vecais Apraksts'],
        'slug'        => ['lv' => 'vecais-nosaukums'],
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->set('title', 'Produkts ar Latviešu Simboliem')
            ->set('description', 'Jaunais Apraksts')
            ->call('update');

    $product->refresh();
    expect($product->slug)->toBe('produkts-ar-latviesu-simboliem');

    // Test English locale
    app()->setLocale('en');
    $englishProduct = Product::factory()->create([
        'title'       => ['en' => 'Old English Title'],
        'description' => ['en' => 'Old Description'],
        'slug'        => ['en' => 'old-english-title'],
    ]);

    Livewire::test(EditProduct::class, ['product' => $englishProduct->id])
            ->set('title', 'New Product with Symbols')
            ->set('description', 'New Description')
            ->call('update');

    $englishProduct->refresh();
    expect($englishProduct->title)->toBe('New Product with Symbols')
                                  ->and($englishProduct->slug)->toBe('new-product-with-symbols');
});
