<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
        <link rel="manifest" href="/site.webmanifest" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-main">
        <div class="bg-ss flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <div class="mb-6">
                <x-application-logo class="h-20 w-20 fill-current text-gray-500" />
            </div>

            <div class="mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
