<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"/>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"/>
    <link rel="manifest" href="/site.webmanifest"/>

    <title>
        {{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}
    </title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-main">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-ss">
    <div class="mb-2">
        <x-application-logo class="w-20 h-20 fill-current text-gray-500"/>
    </div>

    <div class="w-full sm:max-w-md px-6 py-4 overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
</html>
