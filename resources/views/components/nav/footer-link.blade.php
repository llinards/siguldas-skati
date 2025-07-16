<li>
    <a
        {{
            $attributes->merge([
                'class' => 'border-1 border-transparent cursor-pointer duration-200 ease-in-out
                                                                                                                                                                                                  hover:border-b-white transition-all',
            ])
        }}
    >
        {{ $slot }}
    </a>
</li>
