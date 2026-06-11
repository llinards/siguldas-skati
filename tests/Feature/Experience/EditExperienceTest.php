<?php

use App\Livewire\Admin\Experience\EditExperience;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully when mounted with an existing experience', function () {
    $experience = Experience::factory()->create([
        'title' => ['lv' => 'Test Experience', 'en' => 'Test Experience EN'],
        'description' => ['lv' => '<p>LV</p>', 'en' => '<p>EN</p>'],
        'is_active' => true,
    ]);

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->assertStatus(200)
        ->assertSet('title', 'Test Experience')
        ->assertSet('description', '<p>LV</p>')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing', function () {
    $experience = Experience::factory()->create();

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->set('title', '')
        ->set('description', '')
        ->call('save')
        ->assertHasErrors(['title', 'description']);
});

test('successfully updates the experience title and description for the active locale', function () {
    $experience = Experience::factory()->create([
        'title' => ['lv' => 'Old Title', 'en' => 'EN Title'],
        'description' => ['lv' => '<p>Old</p>', 'en' => '<p>EN</p>'],
        'is_active' => false,
    ]);

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->set('title', 'New Title')
        ->set('description', '<p>New</p>')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/lv/dashboard/experiences');

    $experience->refresh();

    expect($experience->getTranslation('title', 'lv'))->toBe('New Title')
        ->and($experience->getTranslation('title', 'en'))->toBe('EN Title')
        ->and($experience->getTranslation('description', 'lv'))->toBe('<p>New</p>')
        ->and($experience->is_active)->toBeTrue();
});

test('validates icon image file size on update', function () {
    $experience = Experience::factory()->create();
    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600);

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->set('title', 'Valid title')
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

test('successfully updates the experience with a new icon', function () {
    $experience = Experience::factory()->create();
    $originalIcon = $experience->icon_image;

    $newIcon = UploadedFile::fake()->image('new-icon.svg')->size(8);

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->set('title', 'Updated Experience')
        ->set('icon_image', $newIcon)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/experiences');

    $experience->refresh();

    expect($experience->icon_image)->not()->toBeNull()
        ->and($experience->icon_image)->not()->toBe($originalIcon);

    Storage::disk('public')->assertExists($experience->icon_image);
});

test('updates the experience without changing the icon when no new image is provided', function () {
    $existingIconPath = 'experience-icons/existing-icon.svg';
    Storage::disk('public')->put($existingIconPath, 'fake-svg-content');

    $experience = Experience::factory()->create(['icon_image' => $existingIconPath]);

    Livewire::test(EditExperience::class, ['experience' => $experience])
        ->set('title', 'New Title')
        ->set('description', '<p>New</p>')
        ->call('save')
        ->assertRedirect('/lv/dashboard/experiences');

    expect($experience->fresh()->icon_image)->toBe($existingIconPath);
});
