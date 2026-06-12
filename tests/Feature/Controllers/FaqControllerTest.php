<?php

use App\Models\Faq;
use App\Models\SiteSetting;
use App\Services\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('faq page displays only active faqs', function () {
    Faq::factory()->count(3)->create(['is_active' => true]);
    Faq::factory()->count(2)->create(['is_active' => false]);

    $response = $this->get(route('faq'));

    $response->assertStatus(200)
        ->assertViewIs('faq')
        ->assertViewHas('faqs', function ($faqs) {
            return $faqs->count() === 3 &&
                   $faqs->every(fn ($faq) => (bool) $faq->is_active);
        });
});

test('faq page renders the question and answer of an active faq', function () {
    Faq::factory()->create([
        'is_active' => true,
        'question' => ['lv' => 'Kur atrodas Siguldas Skati?', 'en' => 'Where is it?'],
        'answer' => ['lv' => '<p>Siguldas centrā.</p>', 'en' => '<p>In Sigulda.</p>'],
    ]);

    $response = $this->get('/lv/buj');

    $response->assertStatus(200)
        ->assertSee('Kur atrodas Siguldas Skati?')
        ->assertSee('Siguldas centrā.', false);
});

test('faq page hides inactive faqs', function () {
    Faq::factory()->create([
        'is_active' => false,
        'question' => ['lv' => 'Slēpts jautājums', 'en' => 'Hidden'],
    ]);

    $response = $this->get('/lv/buj');

    $response->assertStatus(200)
        ->assertDontSee('Slēpts jautājums');
});

test('faq page falls back to default title when no setting exists', function () {
    $response = $this->get('/lv/buj');

    $response->assertStatus(200)
        ->assertSee('Biežāk uzdotie jautājumi');
});

test('faq page uses the configured title and subtitle', function () {
    $service = app(SiteSettingService::class);
    $service->set(SiteSetting::KEY_FAQ_TITLE, ['lv' => 'Mani jautājumi', 'en' => 'My questions']);
    $service->set(SiteSetting::KEY_FAQ_SUBTITLE, ['lv' => 'Mans apraksts', 'en' => 'My subtitle']);

    $response = $this->get('/lv/buj');

    $response->assertStatus(200)
        ->assertSee('Mani jautājumi')
        ->assertSee('Mans apraksts');
});
