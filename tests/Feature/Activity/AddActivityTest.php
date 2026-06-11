<?php

use App\Livewire\Admin\Activity\AddActivity;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully', function () {
    Livewire::test(AddActivity::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('modal_heading', '')
        ->assertSet('modal_content', '')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing', function () {
    Livewire::test(AddActivity::class)
        ->set('title', '')
        ->set('modal_heading', '')
        ->set('modal_content', '')
        ->call('save')
        ->assertHasErrors(['title', 'modal_heading', 'modal_content', 'image']);
});

test('validates image is required', function () {
    Livewire::test(AddActivity::class)
        ->set('title', 'Dabas takas')
        ->set('modal_heading', 'Heading')
        ->set('modal_content', '<p>Content</p>')
        ->call('save')
        ->assertHasErrors(['image']);
});

test('validates image file size', function () {
    $oversized = UploadedFile::fake()->image('card.jpg')->size(600);

    Livewire::test(AddActivity::class)
        ->set('title', 'Dabas takas')
        ->set('modal_heading', 'Heading')
        ->set('modal_content', '<p>Content</p>')
        ->set('image', $oversized)
        ->call('save')
        ->assertHasErrors(['image']);
});

test('successfully creates an activity and stores its image', function () {
    $image = UploadedFile::fake()->image('card.jpg')->size(100);

    Livewire::test(AddActivity::class)
        ->set('title', 'Dabas takas')
        ->set('modal_heading', 'Siguldas dabas takas')
        ->set('modal_content', '<p>Takas apraksts</p>')
        ->set('is_active', true)
        ->set('image', $image)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/activities');

    $activity = Activity::first();

    expect(Activity::count())->toBe(1)
        ->and($activity->getTranslation('title', 'lv'))->toBe('Dabas takas')
        ->and($activity->getTranslation('modal_heading', 'lv'))->toBe('Siguldas dabas takas')
        ->and($activity->getTranslation('modal_content', 'lv'))->toBe('<p>Takas apraksts</p>')
        ->and($activity->is_active)->toBeTrue()
        ->and($activity->image)->not()->toBeNull();

    Storage::disk('public')->assertExists($activity->image);
});

test('creates an inactive activity when the checkbox is unchecked', function () {
    $image = UploadedFile::fake()->image('card.jpg')->size(100);

    Livewire::test(AddActivity::class)
        ->set('title', 'Dabas takas')
        ->set('modal_heading', 'Heading')
        ->set('modal_content', '<p>Content</p>')
        ->set('is_active', false)
        ->set('image', $image)
        ->call('save')
        ->assertRedirect('/lv/dashboard/activities');

    expect(Activity::first()->is_active)->toBeFalse();
});
