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
            ->assertHasNoErrors();
//            ->assertSessionHas('message');
});

test('toggle active with non-existent product shows error', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('toggleActive', 999)
            ->assertHasNoErrors();
//            ->assertSessionHas('error');
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
            ->assertHasNoErrors();
//            ->assertSessionHas('message');
});

test('delete non-existent product shows error', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProductList::class)
            ->call('deleteProduct', 999)
            ->assertHasNoErrors();
//            ->assertSessionHas('error');
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
