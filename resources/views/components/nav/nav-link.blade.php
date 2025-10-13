@props(['active' => false])

<li class="xl:mt-2 2xl:mt-0">
    <a
        {{
            $attributes->class([
                'mr-4.5 border-b-1 text-center border-transparent hover:border-b-menu-hover hover:text-menu-hover transition-all
                 ease-in-out duration-200 text-btn-md xl:text-btn',
                'xl:hover:border-b-white xl:hover:text-white' => Route::is('home'),
            ])
        }}
    >
        {{ $slot }}
    </a>
</li>
