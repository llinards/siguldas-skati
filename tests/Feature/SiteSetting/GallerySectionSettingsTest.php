<?php

use App\Livewire\Admin\GallerySectionSettings;
use App\Models\Gallery;
use App\Models\GalleryImage;
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
    Livewire::test(GallerySectionSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('subtitle', '');
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_GALLERY_TITLE, ['lv' => 'Galerija', 'en' => 'Gallery']);
    $service->set(SiteSetting::KEY_GALLERY_SUBTITLE, ['lv' => 'Skati', 'en' => 'Views']);

    Livewire::test(GallerySectionSettings::class)
        ->assertSet('title', 'Galerija')
        ->assertSet('subtitle', 'Skati');
});

test('save persists the values for the active locale', function () {
    Livewire::test(GallerySectionSettings::class)
        ->set('title', 'Jauns virsraksts')
        ->set('subtitle', 'Jauns apakšvirsraksts')
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_GALLERY_TITLE))->toBe(['lv' => 'Jauns virsraksts'])
        ->and($service->getTranslations(SiteSetting::KEY_GALLERY_SUBTITLE))->toBe(['lv' => 'Jauns apakšvirsraksts']);
});

test('saving in one locale preserves the value stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_GALLERY_TITLE, ['en' => 'Gallery']);

    Livewire::test(GallerySectionSettings::class)
        ->set('title', 'Galerija')
        ->set('subtitle', 'Skati')
        ->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_GALLERY_TITLE))
        ->toBe(['en' => 'Gallery', 'lv' => 'Galerija']);
});

test('save requires both fields', function () {
    Livewire::test(GallerySectionSettings::class)
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'subtitle' => 'required']);
});

test('home page renders the saved gallery section title and subtitle', function () {
    Gallery::factory()
        ->has(GalleryImage::factory()->count(1), 'images')
        ->create(['is_active' => true]);

    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_GALLERY_TITLE, ['lv' => 'LV galerijas virsraksts', 'en' => 'EN gallery title']);
    $service->set(SiteSetting::KEY_GALLERY_SUBTITLE, ['lv' => 'LV galerijas teksts', 'en' => 'EN gallery text']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV galerijas virsraksts')
        ->toContain('LV galerijas teksts');
});

test('site-settings route renders the gallery section settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(GallerySectionSettings::class);
});
