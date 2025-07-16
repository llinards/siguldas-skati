@props(['type' => 'link'])

@php
    $styling =
        'bg-ss-dark w-full cursor-pointer rounded-xl border-2 border-transparent px-4 py-2 text-center text-white uppercase transition-all duration-200 hover:border-black hover:bg-white hover:text-black';
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
