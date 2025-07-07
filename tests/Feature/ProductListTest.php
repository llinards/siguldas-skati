<?php

use App\Livewire\Admin\ProductList;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');
});

test('component renders successfully and displays all products regardless of status', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    Livewire::test(ProductList::class)
            ->assertStatus(200)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 5;
            });
});

test('toggles product status between active and inactive with success message', function () {
    $activeProduct   = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    // Test toggling from active to inactive
    Livewire::test(ProductList::class)
            ->call('toggleActive', $activeProduct->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');

    expect($activeProduct->fresh()->is_active)->toBeFalse();

    // Test toggling from inactive to active
    Livewire::test(ProductList::class)
            ->call('toggleActive', $inactiveProduct->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');

    expect($inactiveProduct->fresh()->is_active)->toBeTrue();
});

test('handles toggle status for non-existent product with error message', function () {
    Livewire::test(ProductList::class)
            ->call('toggleActive', 999)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'error');
});

test('deletes product from database and removes cover image from storage', function () {
    $product = Product::factory()->create([
        'cover' => 'products/test-image.jpg',
    ]);

    // Create fake file in storage
    Storage::disk('public')->put('products/test-image.jpg', 'fake image content');
    expect(Storage::disk('public')->exists('products/test-image.jpg'))->toBeTrue();

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');

    expect(Product::find($product->id))->toBeNull()
                                       ->and(Storage::disk('public')->exists('products/test-image.jpg'))->toBeFalse();
});

test('deletes product gracefully when cover image does not exist in storage', function () {
    $product = Product::factory()->create([
        'cover' => 'products/non-existent-image.jpg',
    ]);

    // Verify the file doesn't exist
    expect(Storage::disk('public')->exists('products/non-existent-image.jpg'))->toBeFalse();

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');

    expect(Product::find($product->id))->toBeNull();
});

test('updates product order correctly and shows success message', function () {
    $product1 = Product::factory()->create(['order' => 1]);
    $product2 = Product::factory()->create(['order' => 2]);
    $product3 = Product::factory()->create(['order' => 3]);

    $reorderedProducts = [
        ['value' => $product3->id, 'order' => 1],
        ['value' => $product1->id, 'order' => 2],
        ['value' => $product2->id, 'order' => 3],
    ];

    Livewire::test(ProductList::class)
            ->call('updateProductOrder', $reorderedProducts)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');

    expect($product1->fresh()->order)->toBe(2)
                                     ->and($product2->fresh()->order)->toBe(3)
                                     ->and($product3->fresh()->order)->toBe(1);
});

test('refreshes product list after performing actions', function () {
    Product::factory()->count(3)->create(['is_active' => true]);
    $productToDelete = Product::factory()->create(['is_active' => false]);

    $component = Livewire::test(ProductList::class);

    // Verify initial count and active status
    $component->assertViewHas('products', function ($products) {
        return $products->count() === 4 && $products->where('is_active', true)->count() === 3;
    });

    // Toggle one product status
    $productToToggle = Product::first();
    $component->call('toggleActive', $productToToggle->id);

    // Verify updated active count
    $component->assertViewHas('products', function ($products) {
        return $products->where('is_active', true)->count() === 2;
    });

    // Delete product
    $component->call('deleteProduct', $productToDelete->id);

    // Verify updated total count
    $component->assertViewHas('products', function ($products) {
        return $products->count() === 3;
    });
});
