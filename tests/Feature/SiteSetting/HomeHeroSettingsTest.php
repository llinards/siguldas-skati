<?php

use App\Livewire\Admin\HomeHeroSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('mount loads empty values when no setting exists', function () {
    Livewire::test(HomeHeroSettings::class)
        ->assertStatus(200)
        ->assertSet('titleLv', '')
        ->assertSet('titleEn', '');
});

test('mount loads existing translations into per-locale fields', function () {
    $setting = new SiteSetting(['key' => SiteSetting::KEY_HOME_HERO_TITLE]);
    $setting->setTranslations('value', ['lv' => 'Sveiks!', 'en' => 'Hello!']);
    $setting->save();

    Livewire::test(HomeHeroSettings::class)
        ->assertSet('titleLv', 'Sveiks!')
        ->assertSet('titleEn', 'Hello!');
});

test('save persists translations for both locales', function () {
    Livewire::test(HomeHeroSettings::class)
        ->set('titleLv', 'Jauns LV virsraksts')
        ->set('titleEn', 'New EN title')
        ->call('save')
        ->assertHasNoErrors();

    $setting = SiteSetting::where('key', SiteSetting::KEY_HOME_HERO_TITLE)->firstOrFail();

    expect($setting->getTranslations('value'))->toBe([
        'lv' => 'Jauns LV virsraksts',
        'en' => 'New EN title',
    ]);
});

test('save updates existing setting instead of creating a duplicate', function () {
    Livewire::test(HomeHeroSettings::class)
        ->set('titleLv', 'A')
        ->set('titleEn', 'B')
        ->call('save');

    Livewire::test(HomeHeroSettings::class)
        ->set('titleLv', 'C')
        ->set('titleEn', 'D')
        ->call('save');

    expect(SiteSetting::where('key', SiteSetting::KEY_HOME_HERO_TITLE)->count())->toBe(1);
});

test('save requires both locale values', function () {
    Livewire::test(HomeHeroSettings::class)
        ->set('titleLv', '')
        ->set('titleEn', '')
        ->call('save')
        ->assertHasErrors(['titleLv' => 'required', 'titleEn' => 'required']);
});

test('home page renders the saved hero title for the current locale', function () {
    $setting = new SiteSetting(['key' => SiteSetting::KEY_HOME_HERO_TITLE]);
    $setting->setTranslations('value', ['lv' => 'LV Heading', 'en' => 'EN Heading']);
    $setting->save();

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
