@props([
    'href',
    'class' => '',
])

<li>
    <a
        href="{{ $href }}"
        {{
            $attributes->merge([
                'class' => "block transition-colors duration-300 hover:text-menu-hover $class",
            ])
        }}
        target="_blank"
    >
        {{ $slot }}
    </a>
</li>
