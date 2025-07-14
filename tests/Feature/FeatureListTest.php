<?php

use App\Livewire\Admin\Feature\FeatureList;
use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully', function () {
    Livewire::test(FeatureList::class)
        ->assertStatus(200);
});

test('displays all features in correct order', function () {
    $feature1 = Feature::factory()->create(['title' => 'First Feature', 'order' => 1]);
    $feature2 = Feature::factory()->create(['title' => 'Second Feature', 'order' => 2]);
    $feature3 = Feature::factory()->create(['title' => 'Third Feature', 'order' => 3]);

    Livewire::test(FeatureList::class)
        ->assertSee('First Feature')
        ->assertSee('Second Feature')
        ->assertSee('Third Feature');
});

test('displays paginated features', function () {
    Feature::factory()->count(15)->create();

    Livewire::test(FeatureList::class)
        ->assertStatus(200);
});

test('toggles feature status successfully', function () {
    $feature = Feature::factory()->create(['is_active' => true]);

    Livewire::test(FeatureList::class)
        ->call('toggleActive', $feature->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($feature->fresh()->is_active)->toBeFalse();
});

test('deletes feature successfully', function () {
    $feature = Feature::factory()->create();

    Livewire::test(FeatureList::class)
        ->call('deleteFeature', $feature->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect(Feature::find($feature->id))->toBeNull();
});

test('updates feature order successfully', function () {
    $feature1 = Feature::factory()->create(['order' => 1]);
    $feature2 = Feature::factory()->create(['order' => 2]);
    $feature3 = Feature::factory()->create(['order' => 3]);

    $orderData = [
        ['value' => $feature1->id, 'order' => 3],
        ['value' => $feature2->id, 'order' => 1],
        ['value' => $feature3->id, 'order' => 2],
    ];

    Livewire::test(FeatureList::class)
        ->call('updateFeatureOrder', $orderData)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($feature1->fresh()->order)->toBe(3)
        ->and($feature2->fresh()->order)->toBe(1)
        ->and($feature3->fresh()->order)->toBe(2);
});

test('shows active and inactive features with different styling', function () {
    $activeFeature = Feature::factory()->create(['title' => 'Active Feature', 'is_active' => true]);
    $inactiveFeature = Feature::factory()->create(['title' => 'Inactive Feature', 'is_active' => false]);

    Livewire::test(FeatureList::class)
        ->assertSee('Active Feature')
        ->assertSee('Inactive Feature');
});
