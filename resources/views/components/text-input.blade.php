@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'py-2.5 sm:py-3 px-4 border border-gray-300 rounded-lg sm:text-sm disabled:opacity-50 disabled:pointer-events-none']) }}
/>
