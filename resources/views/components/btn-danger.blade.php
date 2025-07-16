<button
    {{
        $attributes->merge([
            'type' => 'submit',
            'class' => 'flex sm:inline-block justify-center text-center px-6 py-4 uppercase bg-red-500 rounded-xl text-white
                                                                                        transition-all
                                                                                        duration-200 border-1
                                                                                        border-transparent
                                                                                        hover:bg-white hover:text-red-500 hover:border-red-500
                                                                                        cursor-pointer',
        ])
    }}
>
    {{ $slot }}
</button>
