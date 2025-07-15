@props(['type' => 'link'])

@php
    $styling = 'px-6 py-4 uppercase hover:bg-ss-dark rounded-xl hover:text-white
                          transition-all duration-200
                          text-center bg-white border-black text-black cursor-pointer';
@endphp

@if ($type === 'button')
    <button {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </a>
@endif
