<button
    {{
        $attributes->merge([
            'class' => "px-3 py-2 uppercase bg-ss-dark rounded-xl
                                        text-white transition-all duration-200
                                        text-center hover:bg-white hover:text-black cursor-pointer",
        ])
    }}
>
    {{ $slot }}
</button>
