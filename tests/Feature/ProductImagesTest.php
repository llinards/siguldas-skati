<?php

use App\Livewire\Admin\Product\ProductImages;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->actingAs($this->user);
  Storage::fake('public');
});

test('component renders successfully when mounted with existing product', function () {
  $product = Product::factory()->create();

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->assertStatus(200)
          ->assertSet('product.id', $product->id)
          ->assertSet('images', []);
});

test('correctly identifies oversized images and calculates file sizes', function () {
  $product        = Product::factory()->create();
  $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600); // 600KB > 512KB
  $normalImage    = UploadedFile::fake()->image('normal.jpg')->size(100);   // 100KB < 512KB

  $component = Livewire::test(ProductImages::class, ['product' => $product->id])
                       ->set('images', [$oversizedImage, $normalImage]);

  expect($component->instance()->isImageOversized(0))->toBeTrue()
                                                     ->and($component->instance()->isImageOversized(1))->toBeFalse()
                                                     ->and($component->instance()->getImageSizeInKB(0))->toBe(600)
                                                     ->and($component->instance()->getImageSizeInKB(1))->toBe(100);
});

test('shows error message when uploading oversized images', function () {
  $product        = Product::factory()->create();
  $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600);

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [$oversizedImage])
          ->assertSessionHas('_flash.new.0', 'error');
});

test('clears error message when all images are within size limit', function () {
  $product     = Product::factory()->create();
  $normalImage = UploadedFile::fake()->image('normal.jpg')->size(100);

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [$normalImage])
          ->assertSessionMissing('error');
});

test('validates required images field and shows appropriate error messages', function () {
  $product = Product::factory()->create();

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [])
          ->call('store')
          ->assertHasErrors(['images' => 'required']);
});

test('successfully stores multiple images to storage and database with success message', function () {
  $product = Product::factory()->create();
  $image1  = UploadedFile::fake()->image('image1.jpg')->size(100);
  $image2  = UploadedFile::fake()->image('image2.jpg')->size(150);

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [$image1, $image2])
          ->call('store')
          ->assertHasNoErrors()
          ->assertSessionHas('_flash.new.0', 'message')
          ->assertSet('images', []);

  // Verify images were stored in database
  expect(ProductImage::where('product_id', $product->id)->count())->toBe(2);

  // Verify files were stored in storage
  $storedImages = ProductImage::where('product_id', $product->id)->get();
  foreach ($storedImages as $storedImage) {
    Storage::disk('public')->assertExists($storedImage->filename);
  }
});

test('removes existing image from storage and database with success message', function () {
  $product = Product::factory()->create();

  // Create and store a fake image
  $imagePath = 'product-images/gallery/test-image.jpg';
  Storage::disk('public')->put($imagePath, 'fake-image-content');

  $productImage = ProductImage::factory()->create([
      'product_id' => $product->id,
      'filename'   => $imagePath,
  ]);

  // Verify image exists before deletion
  Storage::disk('public')->assertExists($imagePath);
  expect(ProductImage::find($productImage->id))->not()->toBeNull();

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->call('removeImage', $productImage->id)
          ->assertSessionHas('_flash.new.0', 'message');

  // Verify image was removed from both storage and database
  Storage::disk('public')->assertMissing($imagePath);
  expect(ProductImage::find($productImage->id))->toBeNull();
});

test('handles removal of image when file does not exist in storage', function () {
  $product = Product::factory()->create();

  $productImage = ProductImage::factory()->create([
      'product_id' => $product->id,
      'filename'   => 'non-existent-file.jpg',
  ]);

  // Verify the file doesn't exist in storage
  Storage::disk('public')->assertMissing('non-existent-file.jpg');

  // This should not throw an error
  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->call('removeImage', $productImage->id)
          ->assertSessionHas('_flash.new.0', 'message');

  // Verify image was removed from database
  expect(ProductImage::find($productImage->id))->toBeNull();
});

test('removes new image from upload array and reorders indices correctly', function () {
  $product = Product::factory()->create();
  $image1  = UploadedFile::fake()->image('image1.jpg')->size(100);
  $image2  = UploadedFile::fake()->image('image2.jpg')->size(150);
  $image3  = UploadedFile::fake()->image('image3.jpg')->size(200);

  $component = Livewire::test(ProductImages::class, ['product' => $product->id])
                       ->set('images', [$image1, $image2, $image3]);

  // Verify initial state
  expect($component->get('images'))->toHaveCount(3);

  // Remove middle image (index 1)
  $component->call('removeNewImage', 1);

  // Verify array was reordered properly
  expect($component->get('images'))->toHaveCount(2);

  // Verify the remaining images are correctly positioned
  $remainingImages = $component->get('images');
  expect($remainingImages[0]->getClientOriginalName())->toBe('image1.jpg')
                                                      ->and($remainingImages[1]->getClientOriginalName())->toBe('image3.jpg');
});

test('re-checks for oversized images after removing new image', function () {
  $product        = Product::factory()->create();
  $normalImage    = UploadedFile::fake()->image('normal.jpg')->size(100);
  $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600);

  $component = Livewire::test(ProductImages::class, ['product' => $product->id])
                       ->set('images', [$normalImage, $oversizedImage]);

  // Should have error due to oversized image
  $component->assertSessionHas('_flash.new.0', 'error');

  // Remove the oversized image
  $component->call('removeNewImage', 1);

  // Error should be cleared since no oversized images remain
  $component->assertSessionMissing('error');
});

test('handles edge cases for image size calculations', function () {
  $product   = Product::factory()->create();
  $component = Livewire::test(ProductImages::class, ['product' => $product->id]);

  // Test with non-existent indices
  expect($component->instance()->isImageOversized(999))->toBeFalse()
                                                       ->and($component->instance()->getImageSizeInKB(999))->toBe(0);
});

test('stores images in correct directory structure', function () {
  $product = Product::factory()->create();
  $image   = UploadedFile::fake()->image('test.jpg')->size(100);

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [$image])
          ->call('store');

  $storedImage = ProductImage::where('product_id', $product->id)->first();

  expect($storedImage->filename)->toStartWith('product-images/gallery/');
  Storage::disk('public')->assertExists($storedImage->filename);
});

test('clears images array after successful storage', function () {
  $product = Product::factory()->create();
  $image   = UploadedFile::fake()->image('test.jpg')->size(100);

  Livewire::test(ProductImages::class, ['product' => $product->id])
          ->set('images', [$image])
          ->call('store')
          ->assertSet('images', []);
});
