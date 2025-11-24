<?php

use App\Livewire\ContactUs;
use App\Mail\ContactUsMail;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
});

it('can be rendered', function () {
    Livewire::test(ContactUs::class)
        ->assertStatus(200);
});

it('has correct initial state', function () {
    Livewire::test(ContactUs::class)
        ->assertSet('firstName', '')
        ->assertSet('lastName', '')
        ->assertSet('phoneNumber', '')
        ->assertSet('email', '')
        ->assertSet('question', '')
        ->assertSet('consent', false);
});

it('can submit form successfully with all fields', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'janis.berzins@example.com')
        ->set('question', 'Vai ir pieejamas mājas vasaras sezonā?')
        ->set('consent', true)
        ->call('save');

    Mail::assertSent(ContactUsMail::class, function ($mail) {
        return $mail->firstName === 'Jānis' &&
               $mail->lastName === 'Bērziņš' &&
               $mail->phoneNumber === '+371 28123456' &&
               $mail->email === 'janis.berzins@example.com' &&
               $mail->question === 'Vai ir pieejamas mājas vasaras sezonā?' &&
               ! empty($mail->ipAddress);
    });
});

it('can submit form without optional question field', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Anna')
        ->set('lastName', 'Kļava')
        ->set('phoneNumber', '29876543')
        ->set('email', 'anna.klava@example.com')
        ->set('consent', true)
        ->call('save');

    Mail::assertSent(ContactUsMail::class, function ($mail) {
        return $mail->firstName === 'Anna' &&
               $mail->lastName === 'Kļava' &&
               $mail->question === '';
    });
});

it('resets form after successful submission', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'janis@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertSet('firstName', '')
        ->assertSet('lastName', '')
        ->assertSet('phoneNumber', '')
        ->assertSet('email', '')
        ->assertSet('question', '')
        ->assertSet('consent', false);
});

it('accepts valid Latvian characters in names', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis-Mārtiņš')
        ->set('lastName', 'Bērziņš-Kļava')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'janis@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertHasNoErrors();

    Mail::assertSent(ContactUsMail::class);
});

it('accepts various valid phone number formats', function ($phoneNumber) {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', $phoneNumber)
        ->set('email', 'janis@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertHasNoErrors();

    Mail::assertSent(ContactUsMail::class);
})->with([
    '+371 28123456',
    '371 28123456',
    '28123456',
    '+371-28-123-456',
    '(371) 28123456',
    '+371 (28) 123-456',
]);

it('preserves form data during validation errors', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'invalid-email')
        ->set('question', 'Test question')
        ->set('consent', true)
        ->call('save')
        ->assertSet('firstName', 'Jānis')
        ->assertSet('lastName', 'Bērziņš')
        ->assertSet('phoneNumber', '+371 28123456')
        ->assertSet('email', 'invalid-email')
        ->assertSet('question', 'Test question')
        ->assertSet('consent', true)
        ->assertHasErrors(['email']);

    Mail::assertNotSent(ContactUsMail::class);
});

it('handles mail sending failure gracefully', function () {
    Mail::shouldReceive('send')
        ->once()
        ->andThrow(new Exception('Mail server error'));

    $mockErrorLogService = Mockery::mock(ErrorLogService::class);
    $mockErrorLogService->shouldReceive('logError')
        ->once()
        ->with('Failed to send message from contact us form.', Mockery::type(Exception::class), []);

    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
    $mockFlashMessageService->shouldReceive('error')
        ->once()
        ->with('Notikusi kļūda! Mēģini vēlreiz vai sazinies ar mums pa telefonu.');

    app()->instance(ErrorLogService::class, $mockErrorLogService);
    app()->instance(FlashMessageService::class, $mockFlashMessageService);

    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'janis@example.com')
        ->set('consent', true)
        ->call('save')
        ->assertSet('firstName', 'Jānis')
        ->assertSet('lastName', 'Bērziņš')
        ->assertSet('phoneNumber', '+371 28123456')
        ->assertSet('email', 'janis@example.com');
});

it('validates required fields', function ($field, $value) {
    $validData = [
        'firstName' => 'Jānis',
        'lastName' => 'Bērziņš',
        'phoneNumber' => '+371 28123456',
        'email' => 'janis@example.com',
        'question' => 'Test question',
        'consent' => true,
    ];

    $validData[$field] = $value;

    Livewire::test(ContactUs::class)
        ->set('firstName', $validData['firstName'])
        ->set('lastName', $validData['lastName'])
        ->set('phoneNumber', $validData['phoneNumber'])
        ->set('email', $validData['email'])
        ->set('question', $validData['question'])
        ->set('consent', $validData['consent'])
        ->call('save')
        ->assertHasErrors([$field => 'required']);

    Mail::assertNotSent(ContactUsMail::class);
})->with([
    ['firstName', ''],
    ['lastName', ''],
    ['phoneNumber', ''],
    ['email', ''],
]);

it('validates field minimum lengths', function ($field, $value) {
    $validData = [
        'firstName' => 'Jānis',
        'lastName' => 'Bērziņš',
        'phoneNumber' => '+371 28123456',
        'email' => 'janis@example.com',
        'question' => 'Test question',
        'consent' => true,
    ];

    $validData[$field] = $value;

    Livewire::test(ContactUs::class)
        ->set('firstName', $validData['firstName'])
        ->set('lastName', $validData['lastName'])
        ->set('phoneNumber', $validData['phoneNumber'])
        ->set('email', $validData['email'])
        ->set('question', $validData['question'])
        ->set('consent', $validData['consent'])
        ->call('save')
        ->assertHasErrors([$field => 'min']);

    Mail::assertNotSent(ContactUsMail::class);
})->with([
    ['firstName', 'A'],
    ['lastName', 'B'],
    ['phoneNumber', '1234567'],
]);

it('validates field maximum lengths', function ($field, $value) {
    $validData = [
        'firstName' => 'Jānis',
        'lastName' => 'Bērziņš',
        'phoneNumber' => '+371 28123456',
        'email' => 'janis@example.com',
        'question' => 'Test question',
        'consent' => true,
    ];

    $validData[$field] = $value;

    Livewire::test(ContactUs::class)
        ->set('firstName', $validData['firstName'])
        ->set('lastName', $validData['lastName'])
        ->set('phoneNumber', $validData['phoneNumber'])
        ->set('email', $validData['email'])
        ->set('question', $validData['question'])
        ->set('consent', $validData['consent'])
        ->call('save')
        ->assertHasErrors([$field => 'max']);

    Mail::assertNotSent(ContactUsMail::class);
})->with([
    ['firstName', str_repeat('A', 51)],
    ['lastName', str_repeat('B', 51)],
    ['phoneNumber', str_repeat('1', 21)],
    ['email', str_repeat('a', 250).'@example.com'],
    ['question', str_repeat('a', 2001)],
]);

it('validates field regex patterns', function ($field, $value) {
    $validData = [
        'firstName' => 'Jānis',
        'lastName' => 'Bērziņš',
        'phoneNumber' => '+371 28123456',
        'email' => 'janis@example.com',
        'question' => 'Test question',
        'consent' => true,
    ];

    $validData[$field] = $value;

    Livewire::test(ContactUs::class)
        ->set('firstName', $validData['firstName'])
        ->set('lastName', $validData['lastName'])
        ->set('phoneNumber', $validData['phoneNumber'])
        ->set('email', $validData['email'])
        ->set('question', $validData['question'])
        ->set('consent', $validData['consent'])
        ->call('save')
        ->assertHasErrors([$field => 'regex']);

    Mail::assertNotSent(ContactUsMail::class);
})->with([
    ['firstName', 'John123'],
    ['firstName', 'Jānis@'],
    ['lastName', 'Smith123'],
    ['lastName', 'Bērziņš@'],
    ['phoneNumber', '28123456abc'],
    ['phoneNumber', '+371abc'],
]);

it('validates email format', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'invalid-email')
        ->set('consent', true)
        ->call('save')
        ->assertHasErrors(['email' => 'email']);

    Mail::assertNotSent(ContactUsMail::class);
});

it('validates consent acceptance', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', 'Jānis')
        ->set('lastName', 'Bērziņš')
        ->set('phoneNumber', '+371 28123456')
        ->set('email', 'janis@example.com')
        ->set('consent', false)
        ->call('save')
        ->assertHasErrors(['consent' => 'accepted']);

    Mail::assertNotSent(ContactUsMail::class);
});

it('validates all fields at once when all are invalid', function () {
    Livewire::test(ContactUs::class)
        ->set('firstName', '')
        ->set('lastName', '')
        ->set('phoneNumber', '')
        ->set('email', '')
        ->set('consent', false)
        ->call('save')
        ->assertHasErrors([
            'firstName',
            'lastName',
            'phoneNumber',
            'email',
            'consent',
        ]);

    Mail::assertNotSent(ContactUsMail::class);
});
