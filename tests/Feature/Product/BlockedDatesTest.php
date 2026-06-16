<?php

use App\Livewire\Admin\Product\BlockedDates;
use App\Models\BlockedDate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('renders with the product', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->assertStatus(200)
        ->assertSet('product.id', $product->id);
});

it('adds a blocked date range', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->set('startDate', '2026-08-01')
        ->set('endDate', '2026-08-05')
        ->set('reason', 'Apkope')
        ->call('addBlock')
        ->assertHasNoErrors()
        ->assertSet('startDate', '');

    $block = BlockedDate::where('product_id', $product->id)->first();
    expect($block)->not->toBeNull()
        ->and($block->start_date->toDateString())->toBe('2026-08-01')
        ->and($block->end_date->toDateString())->toBe('2026-08-05')
        ->and($block->reason)->toBe('Apkope');
});

it('rejects an end date before the start date', function () {
    $product = Product::factory()->create();

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->set('startDate', '2026-08-05')
        ->set('endDate', '2026-08-01')
        ->call('addBlock')
        ->assertHasErrors(['endDate']);
});

it('removes a blocked range', function () {
    $product = Product::factory()->create();
    $block = BlockedDate::create([
        'product_id' => $product->id, 'start_date' => '2026-08-01', 'end_date' => '2026-08-05',
    ]);

    Livewire::test(BlockedDates::class, ['product' => $product->id])
        ->call('removeBlock', $block->id)
        ->assertHasNoErrors();

    expect(BlockedDate::find($block->id))->toBeNull();
});
