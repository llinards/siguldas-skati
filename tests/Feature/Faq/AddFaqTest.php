<?php

use App\Livewire\Admin\Faq\AddFaq;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully', function () {
    Livewire::test(AddFaq::class)
        ->assertStatus(200)
        ->assertSet('question', '')
        ->assertSet('answer', '')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing', function () {
    Livewire::test(AddFaq::class)
        ->set('question', '')
        ->set('answer', '')
        ->call('save')
        ->assertHasErrors(['question', 'answer']);
});

test('successfully creates a faq with translatable fields', function () {
    Livewire::test(AddFaq::class)
        ->set('question', 'Kur atrodas Siguldas Skati?')
        ->set('answer', '<p>Siguldas centrā.</p>')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/lv/dashboard/faqs');

    $faq = Faq::first();

    expect(Faq::count())->toBe(1)
        ->and($faq->getTranslation('question', 'lv'))->toBe('Kur atrodas Siguldas Skati?')
        ->and($faq->getTranslation('answer', 'lv'))->toBe('<p>Siguldas centrā.</p>')
        ->and($faq->is_active)->toBeTrue();
});

test('creates an inactive faq when the checkbox is unchecked', function () {
    Livewire::test(AddFaq::class)
        ->set('question', 'Jautājums')
        ->set('answer', '<p>Atbilde</p>')
        ->set('is_active', false)
        ->call('save')
        ->assertRedirect('/lv/dashboard/faqs');

    expect(Faq::first()->is_active)->toBeFalse();
});
