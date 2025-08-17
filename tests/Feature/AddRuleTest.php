<?php

use App\Livewire\Admin\Rule\AddRule;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('component renders successfully', function () {
    Livewire::test(AddRule::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('is_active', true)
        ->assertSet('section', Rule::SECTION_HOUSE);
});

it('prevents submission when required fields are missing and displays validation errors', function () {
    Livewire::test(AddRule::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title']);
});

it('validates icon image is required', function () {
    Livewire::test(AddRule::class)
        ->set('title', 'Valid title')
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

it('validates icon image file size', function () {
    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600); // Assuming size limit

    Livewire::test(AddRule::class)
        ->set('title', 'Valid title')
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

it('resets form after successful submission', function () {
    $icon = UploadedFile::fake()->image('test-icon.svg')->size(10);

    Livewire::test(AddRule::class)
        ->set('title', 'Test Rule')
        ->set('section', Rule::SECTION_HOUSE)
        ->set('is_active', false)
        ->set('icon_image', $icon)
        ->call('save')
        ->assertRedirect('/lv/dashboard/rules');

    expect(Rule::count())->toBe(1);
});
