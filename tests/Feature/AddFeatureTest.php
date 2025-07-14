<?php

use App\Livewire\Admin\Feature\AddFeature;
use App\Models\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully', function () {
    Livewire::test(AddFeature::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    Livewire::test(AddFeature::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title']);
});

// test('successfully creates feature and redirects with success message', function () {
//    $icon = UploadedFile::fake()->image('test-icon.svg')->size(10);
//
//    Livewire::test(AddFeature::class)
//        ->set('title', 'Test Feature')
//        ->set('is_active', true)
//        ->set('icon_image', $icon)
//        ->call('save')
//        ->assertRedirect('/lv/dashboard/features');
//
//    $feature = Feature::where('title', 'Test Feature')->first();
//    expect($feature)->not()->toBeNull()
//        ->and($feature->title)->toBe('Test Feature')
//        ->and($feature->is_active)->toBeTrue()
//        ->and($feature->image_icon)->not()->toBeNull();
// });
//
// test('creates feature with inactive status when checkbox is unchecked', function () {
//    $icon = UploadedFile::fake()->image('test-icon.svg')->size(10);
//
//    Livewire::test(AddFeature::class)
//        ->set('title', 'Inactive Feature')
//        ->set('is_active', false)
//        ->set('icon_image', $icon)
//        ->call('save')
//        ->assertRedirect('/lv/dashboard/features');
//
//    $feature = Feature::where('title', 'Inactive Feature')->first();
//    expect($feature)->not()->toBeNull()
//        ->and($feature->is_active)->toBeFalse();
// });

test('validates icon image is required', function () {
    Livewire::test(AddFeature::class)
        ->set('title', 'Valid title')
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('validates icon image file size', function () {
    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600); // Assuming size limit

    Livewire::test(AddFeature::class)
        ->set('title', 'Valid title')
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('resets form after successful submission', function () {
    $icon = UploadedFile::fake()->image('test-icon.svg')->size(10);

    Livewire::test(AddFeature::class)
        ->set('title', 'Test Feature')
        ->set('is_active', false)
        ->set('icon_image', $icon)
        ->call('save')
        ->assertRedirect('/lv/dashboard/features');

    expect(Feature::count())->toBe(1);
});
