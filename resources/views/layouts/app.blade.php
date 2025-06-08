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

    <meta name="author" content="{{ config('app.name') }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
    <meta name="description"
          content="@lang('Atklājiet Siguldas Skatus, modernas brīvdienu mājas ar dabu un dizainu, ideālas jūsu atpūtai.')">
    <meta name="keywords" content="@lang('Siguldas Skati, brīvdienu mājas, atpūta, daba, dizains, Sigulda')">

    <meta property="og:url" content="{{Request::url()}}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}">
    <meta property="og:description"
          content="@lang('Atklājiet Siguldas Skatus, modernas brīvdienu mājas ar dabu un dizainu, ideālas jūsu atpūtai.')">
    <meta property="og:image" content="{{ asset('images/ss-meta-logo.svg') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="siguldasskati.lv">
    <meta property="twitter:url" content="{{Request::url()}}">
    <meta name="twitter:title" content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}">
    <meta name="twitter:description"
          content="@lang('Atklājiet Siguldas Skatus, modernas brīvdienu mājas ar dabu un dizainu, ideālas jūsu atpūtai.')">
    <meta name="twitter:image" content="{{ asset('images/ss-meta-logo.svg') }}">
    <meta name="robots" content="index, follow">

    <meta name="geo.region" content="LV">
    <meta name="geo.placename" content="Sigulda, Latvia">

    <link rel="canonical" href="{{ Request::url() }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @cookieconsentscripts
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "{{ config('app.name') }}",
          "url": "{{ config('app.url') }}",
          "logo": "{{ asset('images/ss-meta-logo.svg') }}",
          "description": "Atklājiet Siguldas Skatus, modernas brīvdienu mājas ar dabu un dizainu, ideālas jūsu atpūtai."
        }
    </script>
</head>

<body class="antialiased font-main">
<div>
    @include('includes.nav')

    <!-- Page Content -->
    <main aria-labelledby="main-content">
        {{ $slot }}
    </main>
    @include('includes.footer')
</div>
@cookieconsentview
</body>

</html>
