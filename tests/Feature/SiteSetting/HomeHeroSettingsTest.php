<?php

use App\Livewire\Admin\HomeHeroSettings;
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

test('mount loads empty value when no setting exists', function () {
    Livewire::test(HomeHeroSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '');
});

test('mount loads the value for the active locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_HOME_HERO_TITLE, ['lv' => 'Sveiks!', 'en' => 'Hello!']);

    Livewire::test(HomeHeroSettings::class)
        ->assertSet('title', 'Sveiks!');
});

test('save persists the value for the active locale', function () {
    Livewire::test(HomeHeroSettings::class)
        ->set('title', 'Jauns LV virsraksts')
        ->call('save')
        ->assertHasNoErrors();

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_HOME_HERO_TITLE))
        ->toBe(['lv' => 'Jauns LV virsraksts']);
});

test('saving in one locale preserves the value already stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_HOME_HERO_TITLE, ['en' => 'Existing EN']);

    Livewire::test(HomeHeroSettings::class)
        ->set('title', 'LV vērtība')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_HOME_HERO_TITLE))
        ->toBe(['en' => 'Existing EN', 'lv' => 'LV vērtība']);
});

test('save updates existing setting instead of creating a duplicate', function () {
    Livewire::test(HomeHeroSettings::class)->set('title', 'A')->call('save');
    Livewire::test(HomeHeroSettings::class)->set('title', 'C')->call('save');

    expect(SiteSetting::where('key', SiteSetting::KEY_HOME_HERO_TITLE)->count())->toBe(1);
});

test('save requires a value', function () {
    Livewire::test(HomeHeroSettings::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

test('home page renders the saved hero title for the current locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_HOME_HERO_TITLE, ['lv' => 'LV Heading', 'en' => 'EN Heading']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())->toContain('LV Heading');
});

test('home page falls back to the translation default when the setting is absent', function () {
    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())->toContain('Modernas brīvdienu dizaina mājas tavai atpūtai!');
});

test('site-settings route is gated by auth', function () {
    auth()->logout();

    $response = $this->get(route('dashboard.site-settings'));

    $response->assertRedirect(route('login'));
});

test('site-settings route renders the hero settings form for authenticated users', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(HomeHeroSettings::class);
});
