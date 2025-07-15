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
        'success' => 'bg-ss-dark text-white',
        'error' => 'bg-red-100 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-100 text-yellow-800',
        'info' => 'border-blue-200 bg-blue-100 text-blue-800',
        'secondary' => 'border-gray-200 bg-gray-50 text-gray-600',
        'dark' => 'border-gray-200 bg-gray-100 text-gray-800',
    ];
    $currentStyle = $styles[$type] ?? $styles['success'];
@endphp

@if ($message)
    <div
        {{ $attributes->merge(['class' => "mb-4 border text-sm rounded-3xl p-4 {$currentStyle}"]) }}
        role="alert"
        tabindex="-1"
    >
        {{ $message }}
    </div>
@endif
