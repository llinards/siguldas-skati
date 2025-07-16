@props(['type' => 'link', 'disabled' => false])

@php
    $baseStyles = 'flex sm:inline-block justify-center text-center px-6 py-4 uppercase rounded-xl
                                           transition-all duration-200 border-1 border-transparent';

    $enabledStyles = 'bg-ss-dark hover:border-ss-dark cursor-pointer text-white hover:bg-white hover:text-black';

    $disabledStyles = 'cursor-not-allowed bg-gray-300 text-gray-500';

    $styling = $baseStyles . ' ' . ($disabled ? $disabledStyles : $enabledStyles);
@endphp

@if ($type === 'button')
    <button type="button" @disabled($disabled) {{ $attributes->merge(['class' => $styling]) }}>
        {{ $slot }}
    </button>
@elseif ($type === 'submit')
    <button type="submit" @disabled($disabled) {{ $attributes->merge(['class' => $styling]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => $styling]) }}>
        {{ $slot }}
    </a>
@endif
