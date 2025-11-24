<?php

use App\Livewire\Admin\Rule\EditRule;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('component renders with existing rule data', function () {
    $rule = Rule::factory()->create([
        'title' => ['lv' => 'Sākotnējais', 'en' => 'Initial'],
        'section' => Rule::SECTION_SAFETY,
        'is_active' => false,
    ]);

    Livewire::test(EditRule::class, ['rule' => $rule])
        ->assertStatus(200)
        ->assertSet('title', $rule->title)
        ->assertSet('section', $rule->section)
        ->assertSet('is_active', $rule->is_active);
});

it('shows validation errors when required fields are missing', function () {
    $rule = Rule::factory()->create();

    Livewire::test(EditRule::class, ['rule' => $rule])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title']);
});

it('validates optional icon image when provided (size/mime)', function () {
    $rule = Rule::factory()->create();

    $oversizedFile = UploadedFile::fake()->image('large-icon.svg')->size(600);

    Livewire::test(EditRule::class, ['rule' => $rule])
        ->set('icon_image', $oversizedFile)
        ->call('save')
        ->assertHasErrors(['icon_image']);
});

it('updates rule without changing icon and redirects', function () {
    $rule = Rule::factory()->create([
        'title' => ['lv' => 'Vecs', 'en' => 'Old'],
        'section' => Rule::SECTION_HOUSE,
        'is_active' => true,
    ]);

    Livewire::test(EditRule::class, ['rule' => $rule])
        ->set('title', 'Jauns')
        ->set('section', Rule::SECTION_SAFETY)
        ->set('is_active', false)
        ->call('save')
        ->assertRedirect('/lv/dashboard/rules');

    $rule->refresh();

    expect($rule->title)->toBe('Jauns')
        ->and($rule->section)->toBe(Rule::SECTION_SAFETY)
        ->and($rule->is_active)->toBeFalse();
});

it('updates rule with new icon and redirects', function () {
    $rule = Rule::factory()->create([
        'title' => ['lv' => 'Vecs', 'en' => 'Old'],
        'section' => Rule::SECTION_HOUSE,
        'is_active' => true,
    ]);

    $icon = UploadedFile::fake()->image('test-icon.svg')->size(10);

    Livewire::test(EditRule::class, ['rule' => $rule])
        ->set('title', 'Atjaunots')
        ->set('icon_image', $icon)
        ->call('save')
        ->assertRedirect('/lv/dashboard/rules');

    $rule->refresh();

    expect($rule->title)->toBe('Atjaunots');
});
