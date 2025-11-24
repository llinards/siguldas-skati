<?php

use App\Livewire\Newsletter;
use App\Models\Newsletter as NewsletterModel;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can be rendered', function () {
    Livewire::test(Newsletter::class)
        ->assertStatus(200);
});

it('has correct initial state', function () {
    Livewire::test(Newsletter::class)
        ->assertSet('email', '')
        ->assertSet('consent', false);
});

it('can submit form successfully', function () {
    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(NewsletterModel::count())->toBe(1);
    expect(NewsletterModel::first()->email)->toBe('test@example.com');
});

it('resets form after successful submission', function () {
    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertSet('email', '')
        ->assertSet('consent', false);
});

it('accepts valid email addresses', function ($email) {
    Livewire::test(Newsletter::class)
        ->set('email', $email)
        ->set('consent', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(NewsletterModel::count())->toBe(1);
    expect(NewsletterModel::first()->email)->toBe($email);
})->with([
    'test@example.com',
    'user.name@example.com',
    'user+tag@example.com',
    'janis.berzins@example.lv',
    'anna@subdomain.example.com',
]);

it('preserves form data during validation errors', function () {
    Livewire::test(Newsletter::class)
        ->set('email', 'invalid-email')
        ->set('consent', true)
        ->call('save')
        ->assertSet('email', 'invalid-email')
        ->assertSet('consent', true)
        ->assertHasErrors(['email']);

    expect(NewsletterModel::count())->toBe(0);
});

it('handles newsletter service failure gracefully', function () {
    $mockNewsletterService = Mockery::mock(NewsletterService::class);
    $mockNewsletterService->shouldReceive('store')
        ->once()
        ->andThrow(new Exception('Database error'));

    $mockErrorLogService = Mockery::mock(ErrorLogService::class);
    $mockErrorLogService->shouldReceive('logError')
        ->once()
        ->with('Failed to subscribe user to newsletters.', Mockery::type(Exception::class), []);

    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
    $mockFlashMessageService->shouldReceive('error')
        ->once()
        ->with('Notikusi kļūda! Mēģini vēlreiz vai sazinies ar mums pa telefonu.');

    app()->instance(NewsletterService::class, $mockNewsletterService);
    app()->instance(ErrorLogService::class, $mockErrorLogService);
    app()->instance(FlashMessageService::class, $mockFlashMessageService);

    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertSet('email', 'test@example.com')
        ->assertSet('consent', true);

    expect(NewsletterModel::count())->toBe(0);
});

it('validates required email field', function () {
    Livewire::test(Newsletter::class)
        ->set('email', '')
        ->set('consent', true)
        ->call('save')
        ->assertHasErrors(['email' => 'required']);

    expect(NewsletterModel::count())->toBe(0);
});

it('validates email format', function ($email) {
    Livewire::test(Newsletter::class)
        ->set('email', $email)
        ->set('consent', true)
        ->call('save')
        ->assertHasErrors(['email' => 'email']);

    expect(NewsletterModel::count())->toBe(0);
})->with([
    'invalid-email',
    'user@',
    '@example.com',
    'user..name@example.com',
    'user name@example.com',
]);

it('validates email maximum length', function () {
    $longEmail = str_repeat('a', 250).'@example.com';

    Livewire::test(Newsletter::class)
        ->set('email', $longEmail)
        ->set('consent', true)
        ->call('save')
        ->assertHasErrors(['email' => 'max']);

    expect(NewsletterModel::count())->toBe(0);
});

it('validates email uniqueness', function () {
    NewsletterModel::factory()->create(['email' => 'test@example.com']);

    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);

    expect(NewsletterModel::count())->toBe(1);
});

it('validates consent acceptance', function () {
    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', false)
        ->call('save')
        ->assertHasErrors(['consent' => 'accepted']);

    expect(NewsletterModel::count())->toBe(0);
});

it('validates all fields at once when all are invalid', function () {
    Livewire::test(Newsletter::class)
        ->set('email', '')
        ->set('consent', false)
        ->call('save')
        ->assertHasErrors([
            'email',
            'consent',
        ]);

    expect(NewsletterModel::count())->toBe(0);
});

it('shows success message after successful subscription', function () {
    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
    $mockFlashMessageService->shouldReceive('success')
        ->once()
        ->with('Paldies! Jūsu pieteikums ir saņemts.');

    app()->instance(FlashMessageService::class, $mockFlashMessageService);

    Livewire::test(Newsletter::class)
        ->set('email', 'test@example.com')
        ->set('consent', true)
        ->call('save');
});

it('handles Latvian characters in email', function () {
    Livewire::test(Newsletter::class)
        ->set('email', 'jānis.bērziņš@example.lv')
        ->set('consent', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(NewsletterModel::count())->toBe(1);
    expect(NewsletterModel::first()->email)->toBe('jānis.bērziņš@example.lv');
});
