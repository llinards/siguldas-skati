<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
    <link rel="manifest" href="/site.webmanifest" />

    <meta name="author" content="{{ config('app.name') }}" />
    <meta name="locale" content="{{ app()->getLocale() }}" />

    <meta property="og:url" content=" {{ Request::url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title"
        content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}" />
    <meta property="og:image" content="{{ $image ?? asset('logo.svg') }}" />

    <title>
        {{ isset($title) ? $title . ' | ' . config('app.name') : 'Drīzumā' . ' | ' . config('app.name') }}
    </title>

    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-[#f2f3ed] text-[#1b1b18] container mx-auto flex flex-col items-center justify-center min-h-screen px-2">
    <img class="w-xs px-4" src="{{asset('images/Siguldas_skati_house_logo.png')}}" />
    <h1 class="uppercase pt-14 pb-6 text-center text-5xl sm:text-6xl md:text-7xl lg:text-8xl lg:leading-20">
        @lang('Tavas brīvdienu')
        <br />
        @lang('dizaina mājas jau pavisamdrīz!')
    </h1>
    <h2 class="uppercase pt-7 pb-4 text-3xl border-t-2">@lang('Pieseko!')</h2>
    <a href="https://www.instagram.com/siguldasskati/" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 448 512">
            <path
                d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
        </svg>
    </a>
</body>

</html>