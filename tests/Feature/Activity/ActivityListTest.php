<?php

use App\Livewire\Admin\Activity\ActivityList;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('component renders successfully', function () {
    Livewire::test(ActivityList::class)
        ->assertStatus(200);
});

test('displays all activities', function () {
    Activity::factory()->create(['title' => ['lv' => 'First Activity', 'en' => 'First']]);
    Activity::factory()->create(['title' => ['lv' => 'Second Activity', 'en' => 'Second']]);

    Livewire::test(ActivityList::class)
        ->assertSee('First Activity')
        ->assertSee('Second Activity');
});

test('toggles activity status successfully', function () {
    $activity = Activity::factory()->create(['is_active' => true]);

    Livewire::test(ActivityList::class)
        ->call('toggleActive', $activity->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($activity->fresh()->is_active)->toBeFalse();
});

test('deletes activity successfully', function () {
    $activity = Activity::factory()->create();

    Livewire::test(ActivityList::class)
        ->call('deleteActivity', $activity->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect(Activity::find($activity->id))->toBeNull();
});

test('updates activity order successfully', function () {
    $activity1 = Activity::factory()->create(['order' => 0]);
    $activity2 = Activity::factory()->create(['order' => 1]);
    $activity3 = Activity::factory()->create(['order' => 2]);

    Livewire::test(ActivityList::class)
        ->call('updateActivityOrder', (string) $activity3->id, 0)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($activity3->fresh()->order)->toBe(0)
        ->and($activity1->fresh()->order)->toBe(1)
        ->and($activity2->fresh()->order)->toBe(2);
});
