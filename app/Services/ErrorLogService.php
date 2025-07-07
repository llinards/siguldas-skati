<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ErrorLogService
{
    public function logError(string $message, \Exception $e, array $context = []): void
    {
        $baseContext = [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        Log::error($message, array_merge($baseContext, $context));
    }

    public function logInfo(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    public function logWarning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }
}
