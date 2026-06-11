<?php

use App\Livewire\Admin\Experience\AddExperience;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully', function () {
    Livewire::test(AddExperience::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('description', '')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    Livewire::test(AddExperience::class)
        ->set('title', '')
        ->set('description', '')
        ->call('save')
        ->assertHasErrors(['title', 'description', 'icon_image']);
});

test('validates icon image is required', function () {
    Livewire::test(AddExperience::class)
        ->set('title', 'Valid title')
        ->set('description', '<p>Valid description</p>')
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('validates icon image file size', function () {
    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600);

    Livewire::test(AddExperience::class)
        ->set('title', 'Valid title')
        ->set('description', '<p>Valid description</p>')
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('successfully creates an experience and stores its icon', function () {
    $icon = UploadedFile::fake()->image('test-icon.svg')->size(8);

    Livewire::test(AddExperience::class)
        ->set('title', 'Test Experience')
        ->set('description', '<p>Test description</p>')
        ->set('is_active', true)
        ->set('icon_image', $icon)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/experiences');

    $experience = Experience::first();

    expect(Experience::count())->toBe(1)
        ->and($experience->getTranslation('title', 'lv'))->toBe('Test Experience')
        ->and($experience->getTranslation('description', 'lv'))->toBe('<p>Test description</p>')
        ->and($experience->is_active)->toBeTrue()
        ->and($experience->icon_image)->not()->toBeNull();

    Storage::disk('public')->assertExists($experience->icon_image);
});

test('creates an inactive experience when the checkbox is unchecked', function () {
    $icon = UploadedFile::fake()->image('test-icon.svg')->size(8);

    Livewire::test(AddExperience::class)
        ->set('title', 'Inactive Experience')
        ->set('description', '<p>Description</p>')
        ->set('is_active', false)
        ->set('icon_image', $icon)
        ->call('save')
        ->assertRedirect('/lv/dashboard/experiences');

    expect(Experience::first()->is_active)->toBeFalse();
});
