<?php

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\FileStorageService;
use App\Services\ProductServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->fileStorageService = $this->mock(FileStorageService::class);
    $this->productServices = new ProductServices($this->fileStorageService);
    Storage::fake('public');
});

test('getAllActiveProducts returns only active products with proper structure', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $activeProducts = $this->productServices->getAllActiveProducts();

    expect($activeProducts)->toHaveCount(3)
        ->and($activeProducts->every(fn ($product) => (bool) $product->is_active))->toBeTrue();

    // Verify product structure
    $activeProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->title)->toBeString()
            ->and($product->slug)->toBeString()
            ->and($product->description)->toBeString();
    });
});

test('getAllOtherProducts excludes specified product and returns only active products', function () {
    $products = Product::factory()->count(5)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $excludedProduct = $products->first();
    $otherProducts = $this->productServices->getAllOtherProducts($excludedProduct);

    expect($otherProducts)->toHaveCount(4)
        ->and($otherProducts->contains('id', $excludedProduct->id))->toBeFalse()
        ->and($otherProducts->every(fn ($product) => (bool) $product->is_active))->toBeTrue();
});

test('getAllProducts returns all products regardless of status', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $allProducts = $this->productServices->getAllProducts();

    expect($allProducts)->toHaveCount(5);

    // Verify each product has proper structure
    $allProducts->each(function ($product) {
        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->description)->toBeString();
    });
});

test('returns empty collections when no products match criteria', function () {
    // Test with no products at all
    expect($this->productServices->getAllActiveProducts())->toBeEmpty()
        ->and($this->productServices->getAllProducts())->toBeEmpty();

    // Test with only inactive products
    Product::factory()->count(3)->create(['is_active' => false]);
    expect($this->productServices->getAllActiveProducts())->toBeEmpty();

    // Test with single product and no others
    $singleProduct = Product::factory()->create(['is_active' => true]);
    expect($this->productServices->getAllOtherProducts($singleProduct))->toBeEmpty();
});

test('getProductById returns existing product with proper structure regardless of status', function () {
    $activeProduct = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    $foundActive = $this->productServices->getProductById($activeProduct->id);
    $foundInactive = $this->productServices->getProductById($inactiveProduct->id);

    expect($foundActive)->toBeInstanceOf(Product::class)
        ->and($foundActive->id)->toBe($activeProduct->id)
        ->and($foundActive->is_active)->toBeTrue()
        ->and($foundActive->description)->toBeString()
        ->and($foundInactive)->toBeInstanceOf(Product::class)
        ->and($foundInactive->id)->toBe($inactiveProduct->id)
        ->and($foundInactive->is_active)->toBeFalse()
        ->and($foundInactive->description)->toBeString();
});

test('getProductById returns null for non-existent product', function () {
    expect($this->productServices->getProductById(999))->toBeNull();
});

test('deleteProduct successfully removes existing product and its cover from database', function () {
    $product = Product::factory()->create([
        'cover' => 'product-images/test.jpg',
    ]);

    // Create some product images
    $productImage1 = ProductImage::factory()->create([
        'product_id' => $product->id,
        'filename' => 'product-images/gallery/image1.jpg',
    ]);

    $productImage2 = ProductImage::factory()->create([
        'product_id' => $product->id,
        'filename' => 'product-images/gallery/image2.jpg',
    ]);

    // Mock file storage service to expect deletion of cover image
    $this->fileStorageService
        ->shouldReceive('deleteFile')
        ->once()
        ->with($product->cover)
        ->andReturn(true);

    // Mock file storage service to expect deletion of product images
    $this->fileStorageService
        ->shouldReceive('deleteFile')
        ->once()
        ->with($productImage1->filename)
        ->andReturn(true);

    $this->fileStorageService
        ->shouldReceive('deleteFile')
        ->once()
        ->with($productImage2->filename)
        ->andReturn(true);

    $result = $this->productServices->deleteProduct($product);

    expect($result)->toBeTrue()
        ->and(Product::find($product->id))->toBeNull();
});

test('toggleProductStatus switches between active and inactive states', function () {
    $activeProduct = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    // Test deactivating active product
    $result1 = $this->productServices->toggleProductStatus($activeProduct->id);
    expect($result1)->toBeTrue()
        ->and($activeProduct->fresh()->is_active)->toBeFalse();

    // Test activating inactive product
    $result2 = $this->productServices->toggleProductStatus($inactiveProduct->id);
    expect($result2)->toBeTrue()
        ->and($inactiveProduct->fresh()->is_active)->toBeTrue();
});

test('toggleProductStatus returns false for non-existent product', function () {
    expect($this->productServices->toggleProductStatus(999))->toBeFalse();
});

test('generateSlug creates proper URL-friendly slugs from various title formats', function () {
    $testCases = [
        'Test Product Title' => 'test-product-title',
        'Test Product! With @Special# Characters$' => 'test-product-with-at-special-characters',
        'Produkts ar latviešu simboliem āēīōū' => 'produkts-ar-latviesu-simboliem-aeiou',
        'Multiple   Spaces    Between Words' => 'multiple-spaces-between-words',
        'UPPERCASE TITLE' => 'uppercase-title',
        'Mixed CaSe TiTlE' => 'mixed-case-title',
    ];

    foreach ($testCases as $input => $expected) {
        expect($this->productServices->generateSlug($input))->toBe($expected);
    }
});

test('createProduct successfully creates a product with provided data', function () {
    $productData = [
        'title' => 'Test Product',
        'description' => 'Test Description',
        'is_active' => true,
        'pricelist' => 'Euro 1',
        'person_count' => 2,
        'slug' => 'test-product',
        'cover' => 'product-images/test.jpg',
    ];

    $product = $this->productServices->createProduct($productData);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->title)->toBe('Test Product')
        ->and($product->description)->toBe('Test Description')
        ->and($product->is_active)->toBeTrue()
        ->and($product->pricelist)->toBe('Euro 1')
        ->and($product->person_count)->toBe(2)
        ->and($product->slug)->toBe('test-product')
        ->and($product->cover)->toBe('product-images/test.jpg');
});

test('updateProduct successfully updates an existing product', function () {
    $product = Product::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'is_active' => false,
    ]);

    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'is_active' => true,
    ];

    $result = $this->productServices->updateProduct($product, $updateData);

    expect($result)->toBeTrue()
        ->and($product->fresh()->title)->toBe('Updated Title')
        ->and($product->fresh()->description)->toBe('Updated Description')
        ->and($product->fresh()->is_active)->toBeTrue();
});

test('storeProductCover stores a product cover image', function () {
    $file = UploadedFile::fake()->image('product.jpg');
    $expectedPath = 'product-images/stored-image.jpg';

    $this->fileStorageService
        ->shouldReceive('storeFile')
        ->once()
        ->with($file, FileStorageService::PRODUCT_IMAGE_PATH)
        ->andReturn($expectedPath);

    $result = $this->productServices->storeProductCover($file);

    expect($result)->toBe($expectedPath);
});

test('updateProductCover deletes old cover and stores new one', function () {
    $product = Product::factory()->create([
        'cover' => 'product-images/old-image.jpg',
    ]);

    $file = UploadedFile::fake()->image('new-product.jpg');
    $expectedPath = 'product-images/new-image.jpg';

    $this->fileStorageService
        ->shouldReceive('deleteFile')
        ->once()
        ->with($product->cover)
        ->andReturn(true);

    $this->fileStorageService
        ->shouldReceive('storeFile')
        ->once()
        ->with($file, FileStorageService::PRODUCT_IMAGE_PATH)
        ->andReturn($expectedPath);

    $result = $this->productServices->updateProductCover($product, $file);

    expect($result)->toBe($expectedPath);
});

test('storeProductGalleryImage stores image and creates database record', function () {
    $product = Product::factory()->create();
    $file = UploadedFile::fake()->image('gallery.jpg');
    $expectedPath = 'product-images/gallery/stored-image.jpg';

    $this->fileStorageService
        ->shouldReceive('storeFile')
        ->once()
        ->with($file, FileStorageService::PRODUCT_GALLERY_PATH)
        ->andReturn($expectedPath);

    $productImage = $this->productServices->storeProductGalleryImage($product->id, $file);

    expect($productImage)->toBeInstanceOf(ProductImage::class)
        ->and($productImage->product_id)->toBe($product->id)
        ->and($productImage->filename)->toBe($expectedPath);
});

test('deleteProductImage deletes image file and database record', function () {
    $product = Product::factory()->create();
    $productImage = ProductImage::create([
        'product_id' => $product->id,
        'filename' => 'product-images/gallery/image-to-delete.jpg',
    ]);

    $this->fileStorageService
        ->shouldReceive('deleteFile')
        ->once()
        ->with($productImage->filename)
        ->andReturn(true);

    $result = $this->productServices->deleteProductImage($productImage->id);

    expect($result)->toBeTrue()
        ->and(ProductImage::find($productImage->id))->toBeNull();
});

test('updateProductOrder successfully updates product order values', function () {
    $product1 = Product::factory()->create(['order' => 1]);
    $product2 = Product::factory()->create(['order' => 2]);
    $product3 = Product::factory()->create(['order' => 3]);

    $orderData = [
        ['value' => $product1->id, 'order' => 3],
        ['value' => $product2->id, 'order' => 1],
        ['value' => $product3->id, 'order' => 2],
    ];

    $this->productServices->updateProductOrder($orderData);

    expect($product1->fresh()->order)->toBe(3)
        ->and($product2->fresh()->order)->toBe(1)
        ->and($product3->fresh()->order)->toBe(2);
});

test('updateImageOrder successfully updates order of product images', function () {
    $product = Product::factory()->create();

    $image1 = ProductImage::factory()->create([
        'product_id' => $product->id,
        'order' => 1,
    ]);

    $image2 = ProductImage::factory()->create([
        'product_id' => $product->id,
        'order' => 2,
    ]);

    $image3 = ProductImage::factory()->create([
        'product_id' => $product->id,
        'order' => 3,
    ]);

    $orderData = [
        ['value' => $image1->id, 'order' => 3],
        ['value' => $image2->id, 'order' => 1],
        ['value' => $image3->id, 'order' => 2],
    ];

    $this->productServices->updateImageOrder($orderData);

    expect($image1->fresh()->order)->toBe(3)
        ->and($image2->fresh()->order)->toBe(1)
        ->and($image3->fresh()->order)->toBe(2);
});

test('updateImageOrder throws exception for non-existent image', function () {
    $orderData = [
        ['value' => 999, 'order' => 1],
    ];

    expect(fn () => $this->productServices->updateImageOrder($orderData))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
