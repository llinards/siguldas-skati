<?php

use App\Livewire\Admin\ProductSectionSettings;
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
    Livewire::test(ProductSectionSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('subtitle', '');
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_PRODUCTS_TITLE, ['lv' => 'Mājas', 'en' => 'Houses']);
    $service->set(SiteSetting::KEY_PRODUCTS_SUBTITLE, ['lv' => 'Atpūta', 'en' => 'Relax']);

    Livewire::test(ProductSectionSettings::class)
        ->assertSet('title', 'Mājas')
        ->assertSet('subtitle', 'Atpūta');
});

test('save persists the values for the active locale', function () {
    Livewire::test(ProductSectionSettings::class)
        ->set('title', 'Jauns virsraksts')
        ->set('subtitle', 'Jauns apakšvirsraksts')
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_PRODUCTS_TITLE))->toBe(['lv' => 'Jauns virsraksts'])
        ->and($service->getTranslations(SiteSetting::KEY_PRODUCTS_SUBTITLE))->toBe(['lv' => 'Jauns apakšvirsraksts']);
});

test('saving in one locale preserves the value stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_PRODUCTS_TITLE, ['en' => 'Houses']);

    Livewire::test(ProductSectionSettings::class)
        ->set('title', 'Mājas')
        ->set('subtitle', 'Atpūta')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_PRODUCTS_TITLE))
        ->toBe(['en' => 'Houses', 'lv' => 'Mājas']);
});

test('save requires both fields', function () {
    Livewire::test(ProductSectionSettings::class)
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'subtitle' => 'required']);
});

test('home page renders the saved product section title and subtitle', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_PRODUCTS_TITLE, ['lv' => 'LV māju virsraksts', 'en' => 'EN houses title']);
    $service->set(SiteSetting::KEY_PRODUCTS_SUBTITLE, ['lv' => 'LV māju teksts', 'en' => 'EN houses text']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV māju virsraksts')
        ->toContain('LV māju teksts');
});

test('site-settings route renders the product section settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(ProductSectionSettings::class);
});
