@props(['type' => 'success', 'message' => null])

@php
    if (!$message) {
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
        'success' => 'bg-teal-100 border-teal-200 text-teal-800 dark:bg-teal-800/10 dark:border-teal-900 dark:text-teal-500',
        'error' => 'bg-red-100 border-red-200 text-red-800 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500',
        'warning' => 'bg-yellow-100 border-yellow-200 text-yellow-800 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500',
        'info' => 'bg-blue-100 border-blue-200 text-blue-800 dark:bg-blue-800/10 dark:border-blue-900 dark:text-blue-500',
        'secondary' => 'bg-gray-50 border-gray-200 text-gray-600 dark:bg-white/10 dark:border-white/10 dark:text-neutral-400',
        'dark' => 'bg-gray-100 border-gray-200 text-gray-800 dark:bg-white/10 dark:border-white/20 dark:text-white',
    ];
    $currentStyle = $styles[$type] ?? $styles['success'];
@endphp

@if($message)
    <div {{ $attributes->merge(['class' => "mb-4 border text-sm rounded-lg p-4 {$currentStyle}"]) }}
         role="alert"
         tabindex="-1">
        {{ $message }}
    </div>
@endif
