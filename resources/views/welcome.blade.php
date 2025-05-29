<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"/>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"/>
    <link rel="manifest" href="/site.webmanifest"/>

    <meta name="author" content="{{ config('app.name') }}"/>
    <meta name="locale" content="{{ app()->getLocale() }}"/>

    <meta property="og:url" content=" {{ Request::url() }}"/>
    <meta property="og:type" content="website"/>
    <meta
        property="og:title"
        content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}"
    />
    <meta property="og:image" content="{{ $image ?? asset('logo.svg') }}"/>

    <title>
        {{ isset($title) ? $title . ' | ' . config('app.name') : 'Drīzumā' . ' | ' . config('app.name') }}
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body
    class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
<div>
    <img class="w-2xl" src="{{asset('logo.svg')}}"/>
</div>
</body>
</html>
