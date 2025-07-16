<li class="lg:mt-2 xl:mt-0">
    @props(['active' => false])
    <a
        {{
            $attributes->class([
                'mx-4 border-b-1 text-center border-transparent hover:border-b-menu-hover hover:text-menu-hover transition-all
                                                                                                                                      ease-in-out duration-200 text-btn-md md:text-btn-md lg:text-btn',
                'lg:hover:border-b-white lg:hover:text-white' => Route::is('home'),
            ])
        }}
    >
        {{ $slot }}
    </a>
</li>
