@props(['type' => 'success', 'message' => null])

@php
    if (! $message) {
        if (session()->has('message')) {
            $message = session('message');
            $type = 'success';
        } elseif (session()->has('error')) {
            $message = session('error');
            $type = 'error';
        } elseif (session()->has('warning')) {
            $message = session('warning');
            $type = 'warning';
        } elseif (session()->has('info')) {
            $message = session('info');
            $type = 'info';
        }
    }

    $styles = [
        'success' => 'border-teal-200 bg-teal-100 text-teal-800 dark:border-teal-900 dark:bg-teal-800/10 dark:text-teal-500',
        'error' => 'border-red-200 bg-red-100 text-red-800 dark:border-red-900 dark:bg-red-800/10 dark:text-red-500',
        'warning' => 'border-yellow-200 bg-yellow-100 text-yellow-800 dark:border-yellow-900 dark:bg-yellow-800/10 dark:text-yellow-500',
        'info' => 'border-blue-200 bg-blue-100 text-blue-800 dark:border-blue-900 dark:bg-blue-800/10 dark:text-blue-500',
        'secondary' => 'border-gray-200 bg-gray-50 text-gray-600 dark:border-white/10 dark:bg-white/10 dark:text-neutral-400',
        'dark' => 'border-gray-200 bg-gray-100 text-gray-800 dark:border-white/20 dark:bg-white/10 dark:text-white',
    ];
    $currentStyle = $styles[$type] ?? $styles['success'];
@endphp

@if ($message)
    <div
        {{ $attributes->merge(['class' => "mb-4 border text-sm rounded-lg p-4 {$currentStyle}"]) }}
        role="alert"
        tabindex="-1"
    >
        {{ $message }}
    </div>
@endif
