<?php

use App\Livewire\Admin\Product\AddProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully without errors', function () {
    Livewire::test(AddProduct::class)
        ->assertStatus(200);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    Livewire::test(AddProduct::class)
        ->set('title', '')
        ->set('description', '')
        ->set('cover', null)
        ->call('store')
        ->assertHasErrors(['title', 'description', 'cover']);
});

test('rejects image files larger than 512kb with appropriate error message', function () {
    // Create a file larger than 512kb
    $file = UploadedFile::fake()->image('large-image.jpg')->size(600);

    Livewire::test(AddProduct::class)
        ->set('title', 'Test Product')
        ->set('description', 'Test Description')
        ->set('cover', $file)
        ->call('store')
        ->assertHasErrors(['cover' => 'Bildes izmērs nedrīkst pārsniegt 512 kb.']);
});

test('successfully creates product with localized data, uploads cover image, and stores file in correct directory',
    function () {
        $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

        Livewire::test(AddProduct::class)
            ->set('title', 'Jaunais Produkts')
            ->set('description', 'Produkta Apraksts')
            ->set('pricelist', '200 EUR par nakti')
            ->set('is_active', true)
            ->set('personCount', 2)
            ->set('cover', $file)
            ->call('store')
            ->assertSessionHas('_flash.new.0', 'message');

        // Check that product was created with localized data
        $this->assertDatabaseHas('products', [
            'title' => json_encode(['lv' => 'Jaunais Produkts']),
            'description' => json_encode(['lv' => 'Produkta Apraksts']),
            'pricelist' => json_encode(['lv' => '200 EUR par nakti']),
            'is_active' => 1,
            'person_count' => 2,
            'slug' => json_encode(['lv' => 'jaunais-produkts']),
            'cover' => 'product-images/'.$file->hashName(),
        ]);

        // Verify that a cover image was stored and file path is correct
        $product = Product::where('title->lv', 'Jaunais Produkts')->first();
        expect($product->cover)->toStartWith('product-images/');

        // Verify file was actually stored in correct directory structure
        $files = Storage::disk('public')->files('product-images');
        expect($files)->toHaveCount(1)
            ->and($files[0])->toStartWith('product-images/');
    });

test('generates proper URL slugs from titles across different locales and handles special characters correctly',
    function () {
        $file = UploadedFile::fake()->image('product-image.jpg', 800, 600)->size(400);

        app()->setLocale('lv');
        Livewire::test(AddProduct::class)
            ->set('title', 'Produkts ar Latviešu Simboliem')
            ->set('description', 'Apraksts')
            ->set('pricelist', '200 EUR par nakti')
            ->set('personCount', 2)
            ->set('is_active', true)
            ->set('cover', $file)
            ->call('store');

        $this->assertDatabaseHas('products', [
            'slug' => json_encode(['lv' => 'produkts-ar-latviesu-simboliem']),
        ]);

        // Clear database for next test
        Product::truncate();

        // Test English locale
        app()->setLocale('en');
        Livewire::test(AddProduct::class)
            ->set('title', 'Product with Symbols')
            ->set('description', 'Description')
            ->set('personCount', 2)
            ->set('pricelist', '200 EUR per night')
            ->set('is_active', true)
            ->set('cover', $file)
            ->call('store');

        $this->assertDatabaseHas('products', [
            'title' => json_encode(['en' => 'Product with Symbols']),
            'description' => json_encode(['en' => 'Description']),
            'person_count' => 2,
            'slug' => json_encode(['en' => 'product-with-symbols']),
            'pricelist' => json_encode(['en' => '200 EUR per night']),
        ]);
    });
