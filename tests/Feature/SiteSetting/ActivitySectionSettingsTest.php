<?php

use App\Livewire\Admin\ActivitySectionSettings;
use App\Models\Activity;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('mount loads empty values when no setting exists', function () {
    Livewire::test(ActivitySectionSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('subtitle', '');
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_ACTIVITIES_TITLE, ['lv' => 'Ko darīt', 'en' => 'What to do']);
    $service->set(SiteSetting::KEY_ACTIVITIES_SUBTITLE, ['lv' => 'Apraksts', 'en' => 'Subtitle']);

    Livewire::test(ActivitySectionSettings::class)
        ->assertSet('title', 'Ko darīt')
        ->assertSet('subtitle', 'Apraksts');
});

test('save persists the values for the active locale', function () {
    Livewire::test(ActivitySectionSettings::class)
        ->set('title', 'Jauns virsraksts')
        ->set('subtitle', 'Jauns apakšvirsraksts')
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_ACTIVITIES_TITLE))->toBe(['lv' => 'Jauns virsraksts'])
        ->and($service->getTranslations(SiteSetting::KEY_ACTIVITIES_SUBTITLE))->toBe(['lv' => 'Jauns apakšvirsraksts']);
});

test('saving in one locale preserves the value stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_ACTIVITIES_TITLE, ['en' => 'What to do']);

    Livewire::test(ActivitySectionSettings::class)
        ->set('title', 'Ko darīt')
        ->set('subtitle', 'Apraksts')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_ACTIVITIES_TITLE))
        ->toBe(['en' => 'What to do', 'lv' => 'Ko darīt']);
});

test('save requires both fields', function () {
    Livewire::test(ActivitySectionSettings::class)
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'subtitle' => 'required']);
});

test('home page renders the saved activity section title and subtitle', function () {
    Activity::factory()->create(['is_active' => true]);

    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_ACTIVITIES_TITLE, ['lv' => 'LV aktivitāšu virsraksts', 'en' => 'EN activities title']);
    $service->set(SiteSetting::KEY_ACTIVITIES_SUBTITLE, ['lv' => 'LV aktivitāšu teksts', 'en' => 'EN activities text']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV aktivitāšu virsraksts')
        ->toContain('LV aktivitāšu teksts');
});

test('site-settings route renders the activity section settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(ActivitySectionSettings::class);
});
