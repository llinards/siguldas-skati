<?php

use App\Services\FlashMessageService;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->flashMessageService = new FlashMessageService();
});

test('success adds success message to session flash', function () {
    $message = 'Success message';

    $this->flashMessageService->success($message);

    expect(session()->has('message'))->toBeTrue()
                                     ->and(session('message'))->toBe($message);
});

test('error adds error message to session flash', function () {
    $message = 'Error message';

    $this->flashMessageService->error($message);

    expect(session()->has('error'))->toBeTrue()
                                   ->and(session('error'))->toBe($message);
});

test('info adds info message to session flash', function () {
    $message = 'Info message';

    $this->flashMessageService->info($message);

    expect(session()->has('info'))->toBeTrue()
                                  ->and(session('info'))->toBe($message);
});

test('warning adds warning message to session flash', function () {
    $message = 'Warning message';

    $this->flashMessageService->warning($message);

    expect(session()->has('warning'))->toBeTrue()
                                     ->and(session('warning'))->toBe($message);
});
