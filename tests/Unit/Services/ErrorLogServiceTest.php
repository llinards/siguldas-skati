<?php

use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->errorLogService = new ErrorLogService();
});

test('logError records error with context', function () {
    Log::shouldReceive('error')
        ->once()
        ->with('Test error message', [
            'error' => 'Exception message',
            'file' => 'TestFile.php',
            'line' => 42,
            'custom' => 'Custom context',
        ]);

    $exception = new Exception('Exception message');

    // Mock file and line information
    $reflection = new ReflectionObject($exception);
    $property = $reflection->getProperty('file');
    $property->setAccessible(true);
    $property->setValue($exception, 'TestFile.php');

    $property = $reflection->getProperty('line');
    $property->setAccessible(true);
    $property->setValue($exception, 42);

    $this->errorLogService->logError('Test error message', $exception, ['custom' => 'Custom context']);
});

test('logInfo records info message with context', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('Test info message', ['key' => 'value']);

    $this->errorLogService->logInfo('Test info message', ['key' => 'value']);
});

test('logWarning records warning message with context', function () {
    Log::shouldReceive('warning')
        ->once()
        ->with('Test warning message', ['key' => 'value']);

    $this->errorLogService->logWarning('Test warning message', ['key' => 'value']);
});
