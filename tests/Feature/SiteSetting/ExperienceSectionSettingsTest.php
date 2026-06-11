<?php

use App\Livewire\Admin\ExperienceSectionSettings;
use App\Models\Experience;
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
    Livewire::test(ExperienceSectionSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('subtitle', '');
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_EXPERIENCES_TITLE, ['lv' => 'Pieredze', 'en' => 'Experience']);
    $service->set(SiteSetting::KEY_EXPERIENCES_SUBTITLE, ['lv' => 'Apraksts', 'en' => 'Subtitle']);

    Livewire::test(ExperienceSectionSettings::class)
        ->assertSet('title', 'Pieredze')
        ->assertSet('subtitle', 'Apraksts');
});

test('save persists the values for the active locale', function () {
    Livewire::test(ExperienceSectionSettings::class)
        ->set('title', 'Jauns virsraksts')
        ->set('subtitle', 'Jauns apakšvirsraksts')
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_EXPERIENCES_TITLE))->toBe(['lv' => 'Jauns virsraksts'])
        ->and($service->getTranslations(SiteSetting::KEY_EXPERIENCES_SUBTITLE))->toBe(['lv' => 'Jauns apakšvirsraksts']);
});

test('saving in one locale preserves the value stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_EXPERIENCES_TITLE, ['en' => 'Experience']);

    Livewire::test(ExperienceSectionSettings::class)
        ->set('title', 'Pieredze')
        ->set('subtitle', 'Apraksts')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_EXPERIENCES_TITLE))
        ->toBe(['en' => 'Experience', 'lv' => 'Pieredze']);
});

test('save requires both fields', function () {
    Livewire::test(ExperienceSectionSettings::class)
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'subtitle' => 'required']);
});

test('home page renders the saved experience section title and subtitle', function () {
    Experience::factory()->create(['is_active' => true]);

    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_EXPERIENCES_TITLE, ['lv' => 'LV pieredzes virsraksts', 'en' => 'EN experience title']);
    $service->set(SiteSetting::KEY_EXPERIENCES_SUBTITLE, ['lv' => 'LV pieredzes teksts', 'en' => 'EN experience text']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV pieredzes virsraksts')
        ->toContain('LV pieredzes teksts');
});

test('site-settings route renders the experience section settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(ExperienceSectionSettings::class);
});
