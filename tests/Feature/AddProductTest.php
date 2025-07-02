<?php

use App\Livewire\Admin\Product\AddProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('add product component can be rendered', function () {
    Livewire::test(AddProduct::class)
            ->assertStatus(200);
});

test('add product component validates required fields', function () {
    Livewire::test(AddProduct::class)
            ->set('title', '')
            ->set('description', '')
            ->call('store')
            ->assertHasErrors(['title', 'description']);
});

test('add product component successfully creates product', function () {
    Livewire::test(AddProduct::class)
            ->set('title', 'Jaunais Produkts')
            ->set('description', 'Produkta Apraksts')
            ->set('is_active', true)
            ->call('store');
//            ->assertSessionHas('message', 'Produkts pievienots.');

    // Check that product was created with localized data
    $this->assertDatabaseHas('products', [
        'title'       => json_encode(['lv' => 'Jaunais Produkts']),
        'description' => json_encode(['lv' => 'Produkta Apraksts']),
        'is_active'   => true,
        'slug'        => json_encode(['lv' => 'jaunais-produkts']),
        'cover'       => 'storage/product-images/siguldas-skati-product-1.jpg',
    ]);
});

test('add product component generates correct slug from latvian title', function () {
    Livewire::test(AddProduct::class)
            ->set('title', 'Produkts ar Latviešu Simboliem')
            ->set('description', 'Apraksts')
            ->set('is_active', true)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'slug' => json_encode(['lv' => 'produkts-ar-latviesu-simboliem']),
    ]);
});

//test('add product component handles exceptions gracefully', function () {
//    // Mock ProductServices to throw an exception
//    $this->mock(ProductServices::class, function ($mock) {
//        $mock->shouldReceive('generateSlug')
//             ->andThrow(new \Exception('Test exception'));
//    });
//
//    Livewire::test(AddProduct::class)
//            ->set('title', 'Testa Produkts')
//            ->set('description', 'Testa Apraksts')
//            ->call('store')
//            ->assertSessionHas('error', 'Radās kļūda. Lūdzu, mēģiniet vēlreiz.');
//});

test('add product component stores data with english locale', function () {
    // Test with English locale
    app()->setLocale('en');

    Livewire::test(AddProduct::class)
            ->set('title', 'English Product')
            ->set('description', 'English Description')
            ->set('is_active', true)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'title'       => json_encode(['en' => 'English Product']),
        'description' => json_encode(['en' => 'English Description']),
    ]);
});

test('add product component generates slug from title regardless of locale', function () {
    // Test that slug generation works consistently across locales
    app()->setLocale('lv');

    Livewire::test(AddProduct::class)
            ->set('title', 'Produkts ar Simboliem')
            ->set('description', 'Apraksts')
            ->set('is_active', true)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'slug' => json_encode(['lv' => 'produkts-ar-simboliem']),
    ]);

    // Clear database for next test
    Product::truncate();

    app()->setLocale('en');

    Livewire::test(AddProduct::class)
            ->set('title', 'Product with Symbols')
            ->set('description', 'Description')
            ->set('is_active', true)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'slug' => json_encode(['en' => 'product-with-symbols']),
    ]);
});
