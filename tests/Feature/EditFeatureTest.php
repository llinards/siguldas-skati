<?php

use App\Livewire\Admin\Feature\EditFeature;
use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully when mounted with existing feature', function () {
    $feature = Feature::factory()->create([
        'title' => 'Test Feature',
        'is_active' => true,
    ]);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->assertStatus(200)
        ->assertSet('title', 'Test Feature')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    $feature = Feature::factory()->create();

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title']);
});

test('successfully updates feature with new data and shows success message', function () {
    $feature = Feature::factory()->create([
        'title' => 'Old Title',
        'is_active' => false,
    ]);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'New Title')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    $feature->refresh();
    expect($feature->title)->toBe('New Title')
        ->and($feature->is_active)->toBeTrue();
});

test('validates icon image file size on update', function () {
    $feature = Feature::factory()->create();
    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600); // > 10kb limit

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'Valid title')
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('successfully updates feature with new icon image', function () {
    $oldIcon = UploadedFile::fake()->image('new-icon.svg')->size(8);
    Storage::fake('public');
    $feature = Feature::factory()->create([
        'title' => 'Test Feature',
        'icon_image' => $oldIcon,
    ]);

    $newIcon = UploadedFile::fake()->image('new-icon.svg')->size(8);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'Updated Feature')
        ->set('icon_image', $newIcon)
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    $feature->refresh();
    expect($feature->title)->toBe('Updated Feature')
        ->and($feature->icon_image)->not()->toBeNull()
        ->and($feature->icon_image)->not()->toBe('feature-icons/old-icon.svg');
});

test('updates feature without changing icon when no new image is provided', function () {
    Storage::fake('public');
    $existingIconPath = 'feature-icons/existing-icon.svg';
    Storage::disk('public')->put($existingIconPath, 'fake-svg-content');

    $feature = Feature::factory()->create([
        'title' => 'Old Title',
        'icon_image' => $existingIconPath,
    ]);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'New Title')
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    $feature->refresh();
    expect($feature->title)->toBe('New Title')
        ->and($feature->icon_image)->toBe($existingIconPath);
});

test('toggles feature status correctly', function () {
    $activeFeature = Feature::factory()->create(['is_active' => true]);
    $inactiveFeature = Feature::factory()->create(['is_active' => false]);

    // Test switching active to inactive
    Livewire::test(EditFeature::class, ['feature' => $activeFeature])
        ->set('is_active', false)
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    expect($activeFeature->fresh()->is_active)->toBeFalse();

    // Test switching inactive to active
    Livewire::test(EditFeature::class, ['feature' => $inactiveFeature])
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    expect($inactiveFeature->fresh()->is_active)->toBeTrue();
});

test('accepts valid SVG icon file', function () {
    $feature = Feature::factory()->create();
    $validIcon = UploadedFile::fake()->image('valid-icon.svg')->size(8);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'Valid title')
        ->set('icon_image', $validIcon)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/features');
});

test('accepts valid PNG icon file', function () {
    $feature = Feature::factory()->create();
    $validIcon = UploadedFile::fake()->image('valid-icon.png')->size(8);

    Livewire::test(EditFeature::class, ['feature' => $feature])
        ->set('title', 'Valid title')
        ->set('icon_image', $validIcon)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/features');
});
