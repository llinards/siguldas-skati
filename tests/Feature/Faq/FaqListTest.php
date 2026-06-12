<?php

use App\Livewire\Admin\Faq\FaqList;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully', function () {
    Livewire::test(FaqList::class)
        ->assertStatus(200);
});

test('displays all faqs', function () {
    Faq::factory()->create(['question' => ['lv' => 'First Question', 'en' => 'First']]);
    Faq::factory()->create(['question' => ['lv' => 'Second Question', 'en' => 'Second']]);

    Livewire::test(FaqList::class)
        ->assertSee('First Question')
        ->assertSee('Second Question');
});

test('toggles faq status successfully', function () {
    $faq = Faq::factory()->create(['is_active' => true]);

    Livewire::test(FaqList::class)
        ->call('toggleActive', $faq->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($faq->fresh()->is_active)->toBeFalse();
});

test('deletes faq successfully', function () {
    $faq = Faq::factory()->create();

    Livewire::test(FaqList::class)
        ->call('deleteFaq', $faq->id)
        ->assertSessionHas('_flash.new.0', 'message');

    expect(Faq::find($faq->id))->toBeNull();
});

test('updates faq order successfully', function () {
    $faq1 = Faq::factory()->create(['order' => 0]);
    $faq2 = Faq::factory()->create(['order' => 1]);
    $faq3 = Faq::factory()->create(['order' => 2]);

    Livewire::test(FaqList::class)
        ->call('updateFaqOrder', (string) $faq3->id, 0)
        ->assertSessionHas('_flash.new.0', 'message');

    expect($faq3->fresh()->order)->toBe(0)
        ->and($faq1->fresh()->order)->toBe(1)
        ->and($faq2->fresh()->order)->toBe(2);
});
