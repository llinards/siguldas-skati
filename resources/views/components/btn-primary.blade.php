@props(['type' => 'link'])

@php
$styling = 'px-6 py-4 uppercase bg-ss-dark rounded-xl text-white transition-all duration-200
text-center w-full sm:w-auto';
$hover = 'hover:bg-white hover:text-black'
@endphp

@if ($type === 'button')
<button {{ $attributes->merge(['class' => "$styling $hover cursor-pointer"]) }}>
    {{ $slot }}
</button>
@elseif($type === 'submit')
<button {{ $attributes->merge(['class' => $styling]) }}>
    {{ $slot }}
</button>
@else
<a {{ $attributes->merge(['class' => "$styling $hover cursor-pointer"]) }}>
    {{ $slot }}
</a>
@endif