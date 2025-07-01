<?php

use App\Livewire\Admin\EditProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('edit product component can be mounted with product', function () {
    $product = Product::factory()->create([
        'title'       => ['en' => 'Test Product', 'lv' => 'Testa Produkts'],
        'description' => ['en' => 'Description', 'lv' => 'Apraksts'],
        'is_active'   => true,
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->assertSet('title', 'Testa Produkts')
            ->assertSet('description', 'Apraksts')
            ->assertSet('is_active', true);
});

test('edit product component validates required fields', function () {
    $product = Product::factory()->create();

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->set('title', '')
            ->set('description', '')
            ->call('update')
            ->assertHasErrors(['title', 'description']);
});

test('edit product component successfully updates product', function () {
    $product = Product::factory()->create([
        'title'       => ['en' => 'Old Title', 'lv' => 'Vecais Nosaukums'],
        'description' => ['en' => 'Old Description', 'lv' => 'Vecais Apraksts'],
        'is_active'   => false,
    ]);

    Livewire::test(EditProduct::class, ['product' => $product->id])
            ->set('title', 'Jaunais Nosaukums')
            ->set('description', 'Jaunais Apraksts')
            ->set('is_active', true)
            ->call('update');
//            ->assertSessionHas('message', 'Produkts atjaunots.');

    $product->refresh();
    expect($product->title)->toBe('Jaunais Nosaukums')
                           ->and($product->description)->toBe('Jaunais Apraksts')
                           ->and($product->is_active)->toBeTrue();
});
