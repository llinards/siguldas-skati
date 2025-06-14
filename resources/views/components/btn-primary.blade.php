@props(['type' => 'link'])

@php
$styling = 'px-6 py-4 uppercase bg-ss-dark rounded-xl text-white transition-all duration-200
text-center w-full border-1 border-transparent hover:bg-white hover:text-black hover:border-ss-dark
cursor-pointer'
@endphp

@if ($type === 'button')
<button {{ $attributes->merge(['class' => "$styling sm:w-auto"]) }}>
    {{ $slot }}
</button>
@elseif($type === 'submit')
<button {{ $attributes->merge(['class' => "$styling"]) }}>
    {{ $slot }}
</button>
@else
<a {{ $attributes->merge(['class' => "$styling sm:w-auto"]) }}>
    {{ $slot }}
</a>
@endif