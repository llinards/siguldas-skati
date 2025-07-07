<div
    {{ $attributes->class(['carousel-nav order-2 mt-6 flex justify-center gap-x-4 sm:mb-6 md:order-none md:mt-0 md:justify-end']) }}
>
    <x-carousels.btn
        id="{{ $prev }}"
        aria-label="Previous"
        class="hover:border-ss-dark border-1 border-transparent transition-all duration-300"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-7">
            <path
                fill-rule="evenodd"
                d="M9.78 4.22a.75.75 0 0 1 0 1.06L7.06 8l2.72 2.72a.75.75 0 1 1-1.06 1.06L5.47 8.53a.75.75 0 0 1 0-1.06l3.25-3.25a.75.75 0 0 1 1.06 0Z"
                clip-rule="evenodd"
            />
        </svg>
    </x-carousels.btn>
    <x-carousels.btn
        id="{{ $next }}"
        aria-label="Next"
        class="hover:border-ss-dark border-1 border-transparent transition-all duration-300"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-7">
            <path
                fill-rule="evenodd"
                d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z"
                clip-rule="evenodd"
            />
        </svg>
    </x-carousels.btn>
</div>
