<?php

use App\Livewire\Admin\ProductList;
use App\Models\Product;
use App\Models\User;
use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');

    // Set up service mocks
    $this->fileStorageService  = $this->mock(FileStorageService::class);
    $this->productServices     = $this->mock(ProductServices::class);
    $this->flashMessageService = $this->mock(FlashMessageService::class);
    $this->errorLogService     = $this->mock(ErrorLogService::class);

    // Bind mocks to container
    $this->app->instance(FileStorageService::class, $this->fileStorageService);
    $this->app->instance(ProductServices::class, $this->productServices);
    $this->app->instance(FlashMessageService::class, $this->flashMessageService);
    $this->app->instance(ErrorLogService::class, $this->errorLogService);
});

test('component renders successfully and displays all products regardless of status', function () {
    $products = collect([
        Product::factory()->create(['is_active' => true]),
        Product::factory()->create(['is_active' => true]),
        Product::factory()->create(['is_active' => true]),
        Product::factory()->create(['is_active' => false]),
        Product::factory()->create(['is_active' => false]),
    ]);

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn($products);

    // Add expectation for potential error logging (allow it but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->assertStatus(200)
            ->assertViewHas('products', function ($viewProducts) use ($products) {
                return $viewProducts->count() === 5;
            });
});

test('toggles product status between active and inactive with success message', function () {
    $activeProduct = Product::factory()->create(['is_active' => true]);

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([$activeProduct]));

    $this->productServices->shouldReceive('toggleProductStatus')
                          ->atLeast()->once()
                          ->with($activeProduct->id)
                          ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
                              ->atLeast()->once()
                              ->with('Produkta statuss veiksmīgi atjaunināts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('toggleActive', $activeProduct->id)
            ->assertHasNoErrors();
});

test('handles toggle status for non-existent product with error message', function () {
    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([]));

    $this->productServices->shouldReceive('toggleProductStatus')
                          ->atLeast()->once()
                          ->with(999)
                          ->andReturn(false);

    $this->flashMessageService->shouldReceive('error')
                              ->atLeast()->once()
                              ->with('Produkts nav atrasts vai nevarēja tikt atjaunināts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('toggleActive', 999)
            ->assertHasNoErrors();
});

test('deletes product from database and shows success message', function () {
    $product = Product::factory()->create();

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([$product]));

    $this->productServices->shouldReceive('getProductById')
                          ->atLeast()->once()
                          ->with($product->id)
                          ->andReturn($product);

    $this->productServices->shouldReceive('deleteProduct')
                          ->atLeast()->once()
                          ->with($product)
                          ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
                              ->atLeast()->once()
                              ->with('Produkts veiksmīgi dzēsts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors();
});

test('handles product not found during deletion with error message', function () {
    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([]));

    $this->productServices->shouldReceive('getProductById')
                          ->atLeast()->once()
                          ->with(999)
                          ->andReturn(null);

    $this->flashMessageService->shouldReceive('error')
                              ->atLeast()->once()
                              ->with('Produkts nav atrasts vai nevarēja tikt dzēsts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('deleteProduct', 999)
            ->assertHasNoErrors();
});

test('updates product order correctly and shows success message', function () {
    $orderData = [
        ['value' => 1, 'order' => 3],
        ['value' => 2, 'order' => 1],
        ['value' => 3, 'order' => 2],
    ];

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([]));

    $this->productServices->shouldReceive('updateProductOrder')
                          ->atLeast()->once()
                          ->with($orderData);

    $this->flashMessageService->shouldReceive('success')
                              ->atLeast()->once()
                              ->with('Secība atjaunota.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('updateProductOrder', $orderData)
            ->assertHasNoErrors();
});

test('handles error during product loading', function () {
    $exception = new \Exception('Database error');

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
                          ->atLeast()->once()
                          ->with('Failed to load products list', $exception, []);

    $this->flashMessageService->shouldReceive('error')
                              ->atLeast()->once()
                              ->with('Ielādējot produktus, radās kļūda. Lūdzu, atsvaidziniet lapu.');

    Livewire::test(ProductList::class)
            ->assertViewHas('products', function ($products) {
                return $products->isEmpty();
            });
});

test('deletes product with cover image', function () {
    $product = Product::factory()->create([
        'cover' => 'product-images/test-image.jpg',
    ]);

    $this->productServices->shouldReceive('getAllProducts')
                          ->atLeast()->once()
                          ->andReturn(collect([$product]));

    $this->productServices->shouldReceive('getProductById')
                          ->atLeast()->once()
                          ->with($product->id)
                          ->andReturn($product);

    $this->productServices->shouldReceive('deleteProduct')
                          ->atLeast()->once()
                          ->with($product)
                          ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
                              ->atLeast()->once()
                              ->with('Produkts veiksmīgi dzēsts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors();
});

test('refreshes product list after performing actions', function () {
    $products = collect([
        Product::factory()->create(['is_active' => true]),
        Product::factory()->create(['is_active' => true]),
        Product::factory()->create(['is_active' => false]),
    ]);

    $this->productServices->shouldReceive('getAllProducts')
                          ->times(3)
                          ->andReturn($products, $products, $products->slice(0, 2));

    $this->productServices->shouldReceive('toggleProductStatus')
                          ->atLeast()->once()
                          ->with($products->first()->id)
                          ->andReturn(true);

    $this->productServices->shouldReceive('getProductById')
                          ->atLeast()->once()
                          ->with($products->last()->id)
                          ->andReturn($products->last());

    $this->productServices->shouldReceive('deleteProduct')
                          ->atLeast()->once()
                          ->with($products->last())
                          ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
                              ->times(2);

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    $component = Livewire::test(ProductList::class);

    // Toggle status of a product
    $component->call('toggleActive', $products->first()->id);

    // Delete a product
    $component->call('deleteProduct', $products->last()->id);
});
