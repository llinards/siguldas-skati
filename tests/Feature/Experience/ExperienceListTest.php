<?php

use App\Livewire\Admin\Experience\ExperienceList;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully', function () {
    Livewire::test(ExperienceList::class)
        ->assertStatus(200);
});

test('displays all experiences', function () {
    Experience::factory()->create(['title' => ['lv' => 'First Experience', 'en' => 'First']]);
    Experience::factory()->create(['title' => ['lv' => 'Second Experience', 'en' => 'Second']]);

    Livewire::test(ExperienceList::class)
        ->assertSee('First Experience')
        ->assertSee('Second Experience');
});

test('toggles experience status successfully', function () {
    $experience = Experience::factory()->create(['is_active' => true]);

    Livewire::test(ExperienceList::class)
        ->call('toggleActive', $experience->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($experience->fresh()->is_active)->toBeFalse();
});

test('deletes experience successfully', function () {
    $experience = Experience::factory()->create();

    Livewire::test(ExperienceList::class)
        ->call('deleteExperience', $experience->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect(Experience::find($experience->id))->toBeNull();
});

test('updates experience order successfully', function () {
    $experience1 = Experience::factory()->create(['order' => 0]);
    $experience2 = Experience::factory()->create(['order' => 1]);
    $experience3 = Experience::factory()->create(['order' => 2]);

    Livewire::test(ExperienceList::class)
        ->call('updateExperienceOrder', (string) $experience3->id, 0)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($experience3->fresh()->order)->toBe(0)
        ->and($experience1->fresh()->order)->toBe(1)
        ->and($experience2->fresh()->order)->toBe(2);
});
