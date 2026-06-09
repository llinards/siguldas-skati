<?php

use App\Livewire\Admin\AboutUsSettings;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function fillValidAboutUs(Testable $component): Testable
{
    return $component
        ->set('title', 'Par mums')
        ->set('subtitle', 'Klusums')
        ->set('heading', 'Siguldas skati')
        ->set('description', '<p>Apraksts</p>');
}

test('mount loads empty values when no setting exists', function () {
    Livewire::test(AboutUsSettings::class)
        ->assertStatus(200)
        ->assertSet('title', '')
        ->assertSet('description', '')
        ->assertSet('currentImage', null);
});

test('mount loads the values for the active locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_ABOUT_TITLE, ['lv' => 'Par mums', 'en' => 'About us']);
    $service->set(SiteSetting::KEY_ABOUT_DESCRIPTION, ['lv' => '<p>LV</p>', 'en' => '<p>EN</p>']);

    Livewire::test(AboutUsSettings::class)
        ->assertSet('title', 'Par mums')
        ->assertSet('description', '<p>LV</p>');
});

test('save persists the text values for the active locale', function () {
    fillValidAboutUs(Livewire::test(AboutUsSettings::class))
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);

    expect($service->getTranslations(SiteSetting::KEY_ABOUT_TITLE))->toBe(['lv' => 'Par mums'])
        ->and($service->getTranslations(SiteSetting::KEY_ABOUT_SUBTITLE))->toBe(['lv' => 'Klusums'])
        ->and($service->getTranslations(SiteSetting::KEY_ABOUT_HEADING))->toBe(['lv' => 'Siguldas skati'])
        ->and($service->getTranslations(SiteSetting::KEY_ABOUT_DESCRIPTION))->toBe(['lv' => '<p>Apraksts</p>']);
});

test('saving in one locale preserves values already stored for another locale', function () {
    app(SiteSettingService::class)->set(SiteSetting::KEY_ABOUT_TITLE, ['en' => 'About us']);

    fillValidAboutUs(Livewire::test(AboutUsSettings::class))->call('save');

    expect(app(SiteSettingService::class)->getTranslations(SiteSetting::KEY_ABOUT_TITLE))
        ->toBe(['en' => 'About us', 'lv' => 'Par mums']);
});

test('save requires all text fields', function () {
    Livewire::test(AboutUsSettings::class)
        ->call('save')
        ->assertHasErrors([
            'title' => 'required',
            'subtitle' => 'required',
            'heading' => 'required',
            'description' => 'required',
        ]);
});

test('uploading an image stores the file and saves its path for all locales', function () {
    fillValidAboutUs(Livewire::test(AboutUsSettings::class))
        ->set('image', UploadedFile::fake()->image('about.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $service = app(SiteSettingService::class);
    $translations = $service->getTranslations(SiteSetting::KEY_ABOUT_IMAGE);

    expect($translations)->toHaveKeys(['lv', 'en'])
        ->and($translations['lv'])->toBe($translations['en'])
        ->and($translations['lv'])->toStartWith(FileStorageService::ABOUT_IMAGE_PATH);

    Storage::disk('public')->assertExists($translations['lv']);
});

test('replacing an image deletes the previous file', function () {
    Storage::disk('public')->put(FileStorageService::ABOUT_IMAGE_PATH.'/old.jpg', 'old');
    app(SiteSettingService::class)->setForAllLocales(
        SiteSetting::KEY_ABOUT_IMAGE,
        FileStorageService::ABOUT_IMAGE_PATH.'/old.jpg'
    );

    fillValidAboutUs(Livewire::test(AboutUsSettings::class))
        ->set('image', UploadedFile::fake()->image('new.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing(FileStorageService::ABOUT_IMAGE_PATH.'/old.jpg');
});

test('an oversized image is rejected', function () {
    fillValidAboutUs(Livewire::test(AboutUsSettings::class))
        ->set('image', UploadedFile::fake()->image('big.jpg')->size(FileStorageService::MAX_FILE_SIZE_KB + 100))
        ->call('save')
        ->assertHasErrors('image');

    expect(SiteSetting::where('key', SiteSetting::KEY_ABOUT_IMAGE)->exists())->toBeFalse();
});

test('home page renders the saved about-us content for the current locale', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_ABOUT_TITLE, ['lv' => 'LV virsraksts', 'en' => 'EN title']);
    $service->set(SiteSetting::KEY_ABOUT_DESCRIPTION, ['lv' => '<p>LV apraksts</p>', 'en' => '<p>EN description</p>']);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())
        ->toContain('LV virsraksts')
        ->toContain('LV apraksts');
});

test('site-settings route renders the about-us settings form', function () {
    $response = $this->get(route('dashboard.site-settings'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(AboutUsSettings::class);
});
