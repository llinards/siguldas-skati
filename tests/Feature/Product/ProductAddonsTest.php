<?php

use App\Enums\AddonPricingType;
use App\Livewire\Admin\Product\ProductAddons;
use App\Models\Addon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id);
});

it('creates a request-only addon with translated name and description', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->set('nameLv', 'Pirts')
        ->set('nameEn', 'Sauna')
        ->set('descLv', 'Pirts par papildu samaksu')
        ->set('descEn', 'Sauna for an extra fee')
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('nameLv', '');

    $addon = Addon::where('product_id', $product->id)->first();
    expect($addon)->not->toBeNull()
        ->and($addon->getTranslation('name', 'lv'))->toBe('Pirts')
        ->and($addon->getTranslation('name', 'en'))->toBe('Sauna')
        ->and($addon->getTranslation('description', 'lv'))->toBe('Pirts par papildu samaksu')
        ->and($addon->price)->toBe(0)
        ->and($addon->pricing_type)->toBe(AddonPricingType::PerStay)
        ->and($addon->is_active)->toBeTrue();
});

it('requires a Latvian name', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->set('nameLv', '')
        ->call('save')
        ->assertHasErrors(['nameLv']);
});

it('edits an existing addon', function () {
    $product = Product::factory()->create();
    $addon = Addon::factory()->for($product)->create(['name' => ['lv' => 'Old', 'en' => 'Old']]);

    Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->call('edit', $addon->id)
        ->assertSet('nameLv', 'Old')
        ->set('nameLv', 'Jauns')
        ->call('save')
        ->assertHasNoErrors();

    expect($addon->fresh()->getTranslation('name', 'lv'))->toBe('Jauns')
        ->and(Addon::where('product_id', $product->id)->count())->toBe(1);
});

it('toggles active and deletes an addon', function () {
    $product = Product::factory()->create();
    $addon = Addon::factory()->for($product)->create(['is_active' => true]);

    $component = Livewire::test(ProductAddons::class, ['product' => $product->id])
        ->call('toggleActive', $addon->id);
    expect($addon->fresh()->is_active)->toBeFalse();

    $component->call('delete', $addon->id);
    expect(Addon::find($addon->id))->toBeNull();
});
