@props(['type' => 'link'])

@php
    $styling = 'px-4 py-2 uppercase bg-ss-dark rounded-xl border-2 border-transparent text-white transition-all duration-200
                                                      text-center cursor-pointer hover:bg-white hover:border-black hover:text-black w-full';
@endphp

@if ($type === 'submit')
    <button {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => "$styling"]) }}>
        {{ $slot }}
    </a>
@endif
