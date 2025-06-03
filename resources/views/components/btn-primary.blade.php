<a {{ $attributes->merge(['class' => 'px-6 py-4 uppercase bg-ss-dark rounded-xl
    border-2 border-transparent
    text-white
    hover:bg-white hover:border-black hover:text-black
    transition-all duration-200']) }}>
    {{ $slot }}
</a>