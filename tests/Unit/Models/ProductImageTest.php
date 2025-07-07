<?php

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('factory creates valid model instance with all required attributes', function () {
    $productImage = ProductImage::factory()->create();

    expect($productImage)->toBeInstanceOf(ProductImage::class)
                         ->and($productImage->product_id)->toBeInt()
                         ->and($productImage->order)->toBeInt()
                         ->and($productImage->created_at)->not->toBeNull()
                                                              ->and($productImage->updated_at)->not->toBeNull();
});

test('belongs to product relationship returns correct product instance', function () {
    $product      = Product::factory()->create();
    $productImage = ProductImage::factory()->create(['product_id' => $product->id]);

    expect($productImage->product())->toBeInstanceOf(BelongsTo::class)
                                    ->and($productImage->product)->toBeInstanceOf(Product::class)
                                    ->and($productImage->product->id)->toBe($product->id);
});

test('global scope orders product images by order column automatically', function () {
    $product = Product::factory()->create();

    $image1 = ProductImage::factory()->create(['product_id' => $product->id, 'order' => 3]);
    $image2 = ProductImage::factory()->create(['product_id' => $product->id, 'order' => 1]);
    $image3 = ProductImage::factory()->create(['product_id' => $product->id, 'order' => 2]);

    $orderedImages = ProductImage::all();

    expect($orderedImages->pluck('id')->toArray())
        ->toBe([$image2->id, $image3->id, $image1->id]);
});

test('multiple product images maintain correct ordering across different products', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();

    ProductImage::factory()->create(['product_id' => $product1->id, 'order' => 10]);
    ProductImage::factory()->create(['product_id' => $product2->id, 'order' => 5]);
    ProductImage::factory()->create(['product_id' => $product1->id, 'order' => 15]);
    ProductImage::factory()->create(['product_id' => $product2->id, 'order' => 1]);

    $allImages = ProductImage::all();

    expect($allImages->pluck('order')->toArray())->toBe([1, 5, 10, 15]);
});

test('product relationship maintains proper foreign key constraint', function () {
    $product1 = Product::factory()->create(['title' => 'First Product']);
    $product2 = Product::factory()->create(['title' => 'Second Product']);

    $image1 = ProductImage::factory()->create(['product_id' => $product1->id]);
    $image2 = ProductImage::factory()->create(['product_id' => $product2->id]);

    expect($image1->product->title)->toBe('First Product')
                                   ->and($image2->product->title)->toBe('Second Product')
                                   ->and($image1->product_id)->toBe($product1->id)
                                   ->and($image2->product_id)->toBe($product2->id);
});

test('boot method applies global scope correctly to all queries', function () {
    $product = Product::factory()->create();

    // Create images in random order
    $images = collect([
        ProductImage::factory()->create(['product_id' => $product->id, 'order' => 100]),
        ProductImage::factory()->create(['product_id' => $product->id, 'order' => 5]),
        ProductImage::factory()->create(['product_id' => $product->id, 'order' => 50]),
        ProductImage::factory()->create(['product_id' => $product->id, 'order' => 1]),
    ]);

    // Test different query methods all respect the global scope
    $allImages   = ProductImage::all();
    $getImages   = ProductImage::get();
    $whereImages = ProductImage::where('product_id', $product->id)->get();

    expect($allImages->pluck('order')->toArray())->toBe([1, 5, 50, 100])
                                                 ->and($getImages->pluck('order')->toArray())->toBe([1, 5, 50, 100])
                                                 ->and($whereImages->pluck('order')->toArray())->toBe([1, 5, 50, 100]);
});

test('database persists product image with correct attributes', function () {
    $product      = Product::factory()->create();
    $productImage = ProductImage::factory()->create([
        'product_id' => $product->id,
        'order'      => 42,
    ]);

    $this->assertDatabaseHas('product_images', [
        'id'         => $productImage->id,
        'product_id' => $product->id,
        'order'      => 42,
    ]);
});
