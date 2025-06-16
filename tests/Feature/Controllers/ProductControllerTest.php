<?php

use App\Models\Product;

test('index method displays products page with active products', function () {
    // Create active products
    Product::factory()->count(3)->create(['is_active' => true]);

    // Create inactive products (these should not be displayed)
    Product::factory()->count(2)->create(['is_active' => false]);

    // Visit the products page
    $response = $this->get(route('products'));

    // Assert the response is successful and contains the products view
    $response->assertStatus(200)
             ->assertViewIs('products')
             ->assertViewHas('products', function ($products) {
                 return $products->count() === 3 &&
                        $products->every(fn($product) => (bool) $product->is_active);
             });
});

test('show method displays product page with other active products', function () {
    // Create active products
    $products = Product::factory()->count(5)->create(['is_active' => true]);

    // Create inactive products (these should not be displayed)
    Product::factory()->count(2)->create(['is_active' => false]);

    // Select one product to view
    $product = $products->first();

    // Visit the product page
    $response = $this->get(route('product', $product));

    // Assert the response is successful and contains the product view
    $response->assertStatus(200)
             ->assertViewIs('product')
             ->assertViewHas('products', function ($otherProducts) use ($product) {
                 return $otherProducts->count() === 4 &&
                        ! $otherProducts->contains('id', $product->id) &&
                        $otherProducts->every(fn($p) => (bool) $p->is_active);
             });
});

test('inactive products cannot be accessed directly', function () {
    // Create an inactive product
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    // Try to visit the inactive product page
    $response = $this->get(route('product', ['product' => $inactiveProduct->slug]));

    // Assert the response is a 404 not found
    $response->assertStatus(404);
});

test('products page shows correct product information', function () {
    // Create active products with specific data
    Product::factory()->count(2)->create([
        'is_active' => true,
        'title'     => [
            'en' => 'Test Product',
            'lv' => 'Testa Produkts',
        ],
    ]);

    $response = $this->get(route('products'));

    $response->assertStatus(200)
             ->assertViewIs('products')
             ->assertViewHas('products', function ($products) {
                 return $products->count() === 2 &&
                        $products->every(function ($product) {
                            return (bool) $product->is_active &&
                                   ! empty($product->title) &&
                                   ! empty($product->slug);
                        });
             });
});

test('product show page excludes current product from other products', function () {
    // Create active products
    $products       = Product::factory()->count(3)->create(['is_active' => true]);
    $currentProduct = $products->first();

    $response = $this->get(route('product', $currentProduct));

    $response->assertStatus(200)
             ->assertViewIs('product')
             ->assertViewHas('products', function ($otherProducts) use ($currentProduct) {
                 return $otherProducts->count() === 2 &&
                        ! $otherProducts->contains('id', $currentProduct->id);
             });
});
