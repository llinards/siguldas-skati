<?php

use App\Livewire\Admin\Product\AddProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

    Livewire::test(AddProduct::class)
            ->set('title', 'Jaunais Produkts')
            ->set('description', 'Produkta Apraksts')
            ->set('is_active', true)
            ->set('cover', $file)
            ->call('store')
            ->assertSessionHas('_flash.new.0', 'message');

    // Check that product was created with localized data
    $this->assertDatabaseHas('products', [
        'title'       => json_encode(['lv' => 'Jaunais Produkts']),
        'description' => json_encode(['lv' => 'Produkta Apraksts']),
        'is_active'   => 1, // Changed from true to 1
        'slug'        => json_encode(['lv' => 'jaunais-produkts']),
        'cover'       => 'storage/product-images/'.$file->hashName(),
    ]);

    // Verify that a cover image was stored (but don't check exact filename)
    $product = Product::where('title->lv', 'Jaunais Produkts')->first();
    expect($product->cover)->toStartWith('storage/product-images/');

    // Verify file was actually stored
//    Storage::fake('public')->assertExists($product->cover);
});

test('add product component generates correct slug from latvian title', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);
    Livewire::test(AddProduct::class)
            ->set('title', 'Produkts ar Latviešu Simboliem')
            ->set('description', 'Apraksts')
            ->set('is_active', true)
            ->set('cover', $file)
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
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);
    // Test with English locale
    app()->setLocale('en');

    Livewire::test(AddProduct::class)
            ->set('title', 'English Product')
            ->set('description', 'English Description')
            ->set('is_active', true)
            ->set('cover', $file)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'title'       => json_encode(['en' => 'English Product']),
        'description' => json_encode(['en' => 'English Description']),
    ]);
});

test('add product component generates slug from title regardless of locale', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);
    // Test that slug generation works consistently across locales
    app()->setLocale('lv');

    Livewire::test(AddProduct::class)
            ->set('title', 'Produkts ar Simboliem')
            ->set('description', 'Apraksts')
            ->set('is_active', true)
            ->set('cover', $file)
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
            ->set('cover', $file)
            ->call('store');

    $this->assertDatabaseHas('products', [
        'slug' => json_encode(['en' => 'product-with-symbols']),
    ]);
});

// Add this to your existing test file

test('add product component validates required cover image', function () {
    Livewire::test(AddProduct::class)
            ->set('title', 'Test Product')
            ->set('description', 'Test Description')
            ->set('cover', null)
            ->call('store')
            ->assertHasErrors(['cover']);
});

test('add product component validates image file size', function () {
    Storage::fake('public');

    // Create a file larger than 512kb
    $file = UploadedFile::fake()->image('large-image.jpg')->size(600);

    Livewire::test(AddProduct::class)
            ->set('title', 'Test Product')
            ->set('description', 'Test Description')
            ->set('cover', $file)
            ->call('store')
            ->assertHasErrors(['cover' => 'Bildes izmērs nedrīkst pārsniegt 512 kb.']);
});

test('add product component successfully uploads and stores image', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

    Livewire::test(AddProduct::class)
            ->set('title', 'Test Product')
            ->set('description', 'Test Description')
            ->set('cover', $file)
            ->set('is_active', true)
            ->call('store')
            ->assertSessionHas('_flash.new.0', 'message');

    // Verify file was stored in correct directory
    Storage::disk('public')->assertExists('product-images/'.$file->hashName());

    // Verify database record contains correct file path
    $this->assertDatabaseHas('products', [
        'title' => json_encode(['lv' => 'Test Product']),
        'cover' => 'storage/product-images/'.$file->hashName(),
    ]);
});


test('add product component resets form after successful submission', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

    $component = Livewire::test(AddProduct::class)
                         ->set('title', 'Test Product')
                         ->set('description', 'Test Description')
                         ->set('cover', $file)
                         ->set('is_active', true)
                         ->call('store');

    // Verify form fields are reset
    $component->assertSet('title', '')
              ->assertSet('description', '')
              ->assertSet('is_active', false)
              ->assertSet('cover', null);
});

test('add product component stores file in correct storage path', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

    Livewire::test(AddProduct::class)
            ->set('title', 'Test Product')
            ->set('description', 'Test Description')
            ->set('is_active', true)
            ->set('cover', $file)
            ->call('store');

    // Verify the file is stored in the correct directory structure
    $files = Storage::disk('public')->files('product-images');
    expect($files)->toHaveCount(1)
                  ->and($files[0])->toStartWith('product-images/');
});
