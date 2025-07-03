<?php

use App\Livewire\Admin\ProductList;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('product list component renders correctly', function () {
    $user = User::factory()->create();

    // Create some products
    Product::factory()->count(3)->create(['is_active' => true]);
    Product::factory()->count(2)->create(['is_active' => false]);

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->assertStatus(200)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 5; // All products should be loaded
            });
});

test('product list shows all products regardless of status', function () {
    $user = User::factory()->create();

    Product::factory()->count(2)->create(['is_active' => true]);
    Product::factory()->count(3)->create(['is_active' => false]);

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 5;
            });
});

test('toggle active changes product status', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('toggleActive', $product->id)
            ->assertHasNoErrors();

    expect($product->fresh()->is_active)->toBeFalse();
});

test('toggle active shows success message', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('toggleActive', $product->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');
});

test('toggle active with non-existent product shows error', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('toggleActive', 999)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'error');
});

test('delete product removes product from database', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors();

    expect(Product::find($product->id))->toBeNull();
});

test('delete product shows success message', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');
});

test('delete non-existent product shows error', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('deleteProduct', 999)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'error');
});

test('component refreshes product list after toggle', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    $component = Livewire::test(ProductList::class);

    // Get initial products
    $initialProducts    = $component->viewData('products');
    $initialActiveCount = $initialProducts->where('is_active', true)->count();

    // Toggle product status
    $component->call('toggleActive', $product->id);

    // Get updated products
    $updatedProducts    = $component->viewData('products');
    $updatedActiveCount = $updatedProducts->where('is_active', true)->count();

    expect($updatedActiveCount)->toBe($initialActiveCount - 1);
});

test('component refreshes product list after delete', function () {
    $user = User::factory()->create();
    Product::factory()->count(3)->create();
    $productToDelete = Product::factory()->create();

    $this->actingAs($user);

    $component = Livewire::test(ProductList::class);

    // Verify initial count
    $component->assertViewHas('products', function ($products) {
        return $products->count() === 4;
    });

    // Delete product
    $component->call('deleteProduct', $productToDelete->id);

    // Verify updated count
    $component->assertViewHas('products', function ($products) {
        return $products->count() === 3;
    });
});

test('update product order updates product order values', function () {
    $user     = User::factory()->create();
    $product1 = Product::factory()->create(['order' => 1]);
    $product2 = Product::factory()->create(['order' => 2]);
    $product3 = Product::factory()->create(['order' => 3]);

    $this->actingAs($user);

    $reorderedProducts = [
        ['value' => $product3->id, 'order' => 1],
        ['value' => $product1->id, 'order' => 2],
        ['value' => $product2->id, 'order' => 3],
    ];

    Livewire::test(ProductList::class)
            ->call('updateProductOrder', $reorderedProducts)
            ->assertHasNoErrors();

    expect($product1->fresh()->order)->toBe(2);
    expect($product2->fresh()->order)->toBe(3);
    expect($product3->fresh()->order)->toBe(1);
});

test('update product order shows success message', function () {
    $user     = User::factory()->create();
    $product1 = Product::factory()->create(['order' => 1]);
    $product2 = Product::factory()->create(['order' => 2]);

    $this->actingAs($user);

    $reorderedProducts = [
        ['value' => $product2->id, 'order' => 1],
        ['value' => $product1->id, 'order' => 2],
    ];

    Livewire::test(ProductList::class)
            ->call('updateProductOrder', $reorderedProducts)
            ->assertHasNoErrors()
            ->assertSessionHas('_flash.new.0', 'message');
});

test('update product order with non-existent product throws exception', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $invalidProducts = [
        ['value' => 999, 'order' => 1],
    ];

    expect(function () use ($invalidProducts) {
        Livewire::test(ProductList::class)
                ->call('updateProductOrder', $invalidProducts);
    })->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
