<?php

use App\Livewire\Admin\Activity\EditActivity;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully when mounted with an existing activity', function () {
    $activity = Activity::factory()->create([
        'title' => ['lv' => 'Dabas takas', 'en' => 'Nature trails'],
        'modal_heading' => ['lv' => 'LV virsraksts', 'en' => 'EN heading'],
        'modal_content' => ['lv' => '<p>LV</p>', 'en' => '<p>EN</p>'],
        'is_active' => true,
    ]);

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->assertStatus(200)
        ->assertSet('title', 'Dabas takas')
        ->assertSet('modal_heading', 'LV virsraksts')
        ->assertSet('modal_content', '<p>LV</p>')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing', function () {
    $activity = Activity::factory()->create();

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->set('title', '')
        ->set('modal_heading', '')
        ->set('modal_content', '')
        ->call('save')
        ->assertHasErrors(['title', 'modal_heading', 'modal_content']);
});

test('successfully updates the translatable fields for the active locale', function () {
    $activity = Activity::factory()->create([
        'title' => ['lv' => 'Old', 'en' => 'EN Title'],
        'modal_heading' => ['lv' => 'Old heading', 'en' => 'EN heading'],
        'modal_content' => ['lv' => '<p>Old</p>', 'en' => '<p>EN</p>'],
        'is_active' => false,
    ]);

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->set('title', 'New')
        ->set('modal_heading', 'New heading')
        ->set('modal_content', '<p>New</p>')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/lv/dashboard/activities');

    $activity->refresh();

    expect($activity->getTranslation('title', 'lv'))->toBe('New')
        ->and($activity->getTranslation('title', 'en'))->toBe('EN Title')
        ->and($activity->getTranslation('modal_heading', 'lv'))->toBe('New heading')
        ->and($activity->getTranslation('modal_content', 'lv'))->toBe('<p>New</p>')
        ->and($activity->is_active)->toBeTrue();
});

test('validates image file size on update', function () {
    $activity = Activity::factory()->create();
    $oversized = UploadedFile::fake()->image('card.jpg')->size(600);

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->set('image', $oversized)
        ->call('save')
        ->assertHasErrors(['image']);
});

test('successfully updates the activity with a new image', function () {
    $activity = Activity::factory()->create();
    $originalImage = $activity->image;

    $newImage = UploadedFile::fake()->image('new-card.jpg')->size(100);

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->set('title', 'Updated')
        ->set('image', $newImage)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/activities');

    $activity->refresh();

    expect($activity->image)->not()->toBeNull()
        ->and($activity->image)->not()->toBe($originalImage);

    Storage::disk('public')->assertExists($activity->image);
});

test('updates the activity without changing the image when no new image is provided', function () {
    $existingImagePath = 'activity-images/existing.jpg';
    Storage::disk('public')->put($existingImagePath, 'fake-content');

    $activity = Activity::factory()->create(['image' => $existingImagePath]);

    Livewire::test(EditActivity::class, ['activity' => $activity])
        ->set('title', 'New title')
        ->set('modal_heading', 'New heading')
        ->set('modal_content', '<p>New</p>')
        ->call('save')
        ->assertRedirect('/lv/dashboard/activities');

    expect($activity->fresh()->image)->toBe($existingImagePath);
});
