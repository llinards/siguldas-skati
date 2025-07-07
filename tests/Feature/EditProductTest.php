<?php

use App\Livewire\Admin\Product\EditProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully when mounted with existing product', function () {
    $product = Product::factory()->create([
        'title' => ['en' => 'Test Product', 'lv' => 'Testa Produkts'],
        'description' => ['en' => 'Description', 'lv' => 'Apraksts'],
        'is_active' => true,
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
        'title' => ['en' => 'Old Title', 'lv' => 'Vecais Nosaukums'],
        'description' => ['en' => 'Old Description', 'lv' => 'Vecais Apraksts'],
        'slug' => ['en' => 'old-title', 'lv' => 'vecais-nosaukums'],
        'is_active' => false,
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
        'title' => ['lv' => 'Vecais Nosaukums'],
        'description' => ['lv' => 'Vecais Apraksts'],
        'slug' => ['lv' => 'vecais-nosaukums'],
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
        'title' => ['en' => 'Old English Title'],
        'description' => ['en' => 'Old Description'],
        'slug' => ['en' => 'old-english-title'],
    ]);

    Livewire::test(EditProduct::class, ['product' => $englishProduct->id])
        ->set('title', 'New Product with Symbols')
        ->set('description', 'New Description')
        ->call('update');

    $englishProduct->refresh();
    expect($englishProduct->title)->toBe('New Product with Symbols')
        ->and($englishProduct->slug)->toBe('new-product-with-symbols');
});

test('validates cover image file size and shows error for oversized files', function () {
    Storage::fake('public');
    $product = Product::factory()->create();

    $oversizedFile = UploadedFile::fake()->image('large-image.jpg')->size(600); // 600KB > 512KB limit

    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('cover', $oversizedFile)
        ->call('update')
        ->assertHasErrors(['cover']);
});

test('successfully updates product with new cover image', function () {
    Storage::fake('public');
    $product = Product::factory()->create([
        'title' => ['lv' => 'Test Product'],
        'description' => ['lv' => 'Test Description'],
    ]);

    $newImage = UploadedFile::fake()->image('new-cover.jpg', 100, 100)->size(100);

    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('cover', $newImage)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();
    expect($product->cover)->not()->toBeNull();
    Storage::disk('public')->assertExists($product->cover);
});

test('deletes old cover image when uploading new one', function () {
    Storage::fake('public');

    // Create old image file
    $oldImagePath = 'product-images/old-cover.jpg';
    Storage::disk('public')->put($oldImagePath, 'fake-image-content');

    $product = Product::factory()->create([
        'title' => ['lv' => 'Test Product'],
        'description' => ['lv' => 'Test Description'],
        'cover' => $oldImagePath,
    ]);

    $newImage = UploadedFile::fake()->image('new-cover.jpg', 100, 100)->size(100);

    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('cover', $newImage)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();

    // Assert old image is deleted
    Storage::disk('public')->assertMissing($oldImagePath);

    // Assert new image exists
    expect($product->cover)->not()->toBeNull();
    Storage::disk('public')->assertExists($product->cover);
});

test('updates product without changing cover when no new image is provided', function () {
    Storage::fake('public');

    $existingImagePath = 'product-images/existing-cover.jpg';
    Storage::disk('public')->put($existingImagePath, 'fake-image-content');

    $product = Product::factory()->create([
        'title' => ['lv' => 'Old Title'],
        'description' => ['lv' => 'Old Description'],
        'cover' => $existingImagePath,
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('title', 'New Title')
        ->set('description', 'New Description')
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();

    // Assert data is updated
    expect($product->title)->toBe('New Title')
        ->and($product->description)->toBe('New Description');

    // Assert cover remains unchanged
    expect($product->cover)->toBe($existingImagePath);
    Storage::disk('public')->assertExists($existingImagePath);
});

test('handles case when old cover file does not exist on disk', function () {
    Storage::fake('public');

    $product = Product::factory()->create([
        'title' => ['lv' => 'Test Product'],
        'description' => ['lv' => 'Test Description'],
        'cover' => 'product-images/non-existent-file.jpg', // File doesn't exist
    ]);

    $newImage = UploadedFile::fake()->image('new-cover.jpg', 100, 100)->size(100);

    // This should not throw an error even though the old file doesn't exist
    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('cover', $newImage)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();
    expect($product->cover)->not()->toBeNull();
    Storage::disk('public')->assertExists($product->cover);
});

test('cover field is nullable and allows updates without image', function () {
    $product = Product::factory()->create([
        'title' => ['lv' => 'Test Product'],
        'description' => ['lv' => 'Test Description'],
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated Description')
        ->call('update')
        ->assertHasNoErrors()
        ->assertSessionHas('_flash.new.0', 'message');

    $product->refresh();
    expect($product->title)->toBe('Updated Title')
        ->and($product->description)->toBe('Updated Description');
});
