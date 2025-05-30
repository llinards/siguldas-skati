@props(['class' => ''])

<a {{ $attributes->merge(['class' => 'px-6 py-2 uppercase bg-white text-btn rounded-xl
    border-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary hover:bg-bg-hover hover:text-white
    hover:border-transparent
    transition-all duration-200 ' .
    $class]) }}>
    {{ $slot }}
</a>