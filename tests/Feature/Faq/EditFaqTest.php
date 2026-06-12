<?php

use App\Livewire\Admin\Faq\EditFaq;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully when mounted with an existing faq', function () {
    $faq = Faq::factory()->create([
        'question' => ['lv' => 'LV jautājums', 'en' => 'EN question'],
        'answer' => ['lv' => '<p>LV</p>', 'en' => '<p>EN</p>'],
        'is_active' => true,
    ]);

    Livewire::test(EditFaq::class, ['faq' => $faq])
        ->assertStatus(200)
        ->assertSet('question', 'LV jautājums')
        ->assertSet('answer', '<p>LV</p>')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing', function () {
    $faq = Faq::factory()->create();

    Livewire::test(EditFaq::class, ['faq' => $faq])
        ->set('question', '')
        ->set('answer', '')
        ->call('save')
        ->assertHasErrors(['question', 'answer']);
});

test('successfully updates the translatable fields for the active locale', function () {
    $faq = Faq::factory()->create([
        'question' => ['lv' => 'Old', 'en' => 'EN question'],
        'answer' => ['lv' => '<p>Old</p>', 'en' => '<p>EN</p>'],
        'is_active' => false,
    ]);

    Livewire::test(EditFaq::class, ['faq' => $faq])
        ->set('question', 'New')
        ->set('answer', '<p>New</p>')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/lv/dashboard/faqs');

    $faq->refresh();

    expect($faq->getTranslation('question', 'lv'))->toBe('New')
        ->and($faq->getTranslation('question', 'en'))->toBe('EN question')
        ->and($faq->getTranslation('answer', 'lv'))->toBe('<p>New</p>')
        ->and($faq->getTranslation('answer', 'en'))->toBe('<p>EN</p>')
        ->and($faq->is_active)->toBeTrue();
});
