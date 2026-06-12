<?php

use App\Livewire\Admin\FaqSectionSettings;
use App\Models\Faq;
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
    Livewire::test(FaqSectionSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('subtitle', '');
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_FAQ_TITLE, ['lv' => 'BUJ', 'en' => 'FAQ']);
    $service->set(SiteSetting::KEY_FAQ_SUBTITLE, ['lv' => 'Apraksts', 'en' => 'Subtitle']);

    Livewire::test(FaqSectionSettings::class)
        ->assertSet('title', 'BUJ')
        ->assertSet('subtitle', 'Apraksts');
});

test('save persists the values for the active locale', function () {
    Livewire::test(FaqSectionSettings::class)
        ->set('title', 'Jauns virsraksts')
        ->set('subtitle', 'Jauns apakšvirsraksts')
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_FAQ_TITLE))->toBe(['lv' => 'Jauns virsraksts'])
        ->and($service->getTranslations(SiteSetting::KEY_FAQ_SUBTITLE))->toBe(['lv' => 'Jauns apakšvirsraksts']);
});

test('saving in one locale preserves the value stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_FAQ_TITLE, ['en' => 'FAQ']);

    Livewire::test(FaqSectionSettings::class)
        ->set('title', 'BUJ')
        ->set('subtitle', 'Apraksts')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_FAQ_TITLE))
        ->toBe(['en' => 'FAQ', 'lv' => 'BUJ']);
});

test('save requires both fields', function () {
    Livewire::test(FaqSectionSettings::class)
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'subtitle' => 'required']);
});

test('faq page renders the saved title and subtitle', function () {
    Faq::factory()->create(['is_active' => true]);

    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_FAQ_TITLE, ['lv' => 'LV BUJ virsraksts', 'en' => 'EN FAQ title']);
    $service->set(SiteSetting::KEY_FAQ_SUBTITLE, ['lv' => 'LV BUJ teksts', 'en' => 'EN FAQ text']);

    $response = $this->get('/lv/buj');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV BUJ virsraksts')
        ->toContain('LV BUJ teksts');
});

test('site-settings route renders the faq section settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(FaqSectionSettings::class);
});
