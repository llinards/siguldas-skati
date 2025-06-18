@props(['type' => 'link'])

@php
    $styling = 'flex sm:inline-block justify-center text-center px-6 py-4 uppercase bg-ss-dark rounded-xl text-white
    transition-all
    duration-200 border-1
    border-transparent
    hover:bg-white hover:text-black hover:border-ss-dark
    cursor-pointer'
@endphp

@if ($type === 'button')
    <button type="button" {{ $attributes->merge(['class' => "$styling "]) }}>
        {{ $slot }}
    </button>
@elseif($type === 'submit')
    <button type="submit" {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </a>
@endif
