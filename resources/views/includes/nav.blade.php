<nav class="absolute w-full z-50" x-data="{ open: false }">
    <div class="container mx-auto flex justify-between items-center px-4 py-8 relative z-50">
        <a href="/{{ app()->getLocale() }}" class="flex items-center">
            <img src="{{ asset('images/siguldas-skati-logo.png') }}" class="w-44 lg:w-48 transition-all duration-300"
                :class="open ? 'invert' : ''" alt="Siguldas Skati Logo" />
        </a>
        <ul class="hidden md:flex uppercase text-white">
            <x-nav.nav-link href="#">
                @lang('Rezervēt')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('Dizaina mājas un sauna')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('BUJ')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('Kontakti')
            </x-nav.nav-link>
        </ul>


        <button x-on:click="open = !open; $dispatch('toggle-menu')" class="md:hidden relative z-50">
            <svg x-show="!open" class="size-12" xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"
                    clip-rule="evenodd" />
            </svg>
            <svg x-show="open" class="size-12 hover:fill-menu-hover duration-200" xmlns="http://www.w3.org/2000/svg"
                fill="black" viewBox="0 0 24 24">
                <path
                    d="M6.4 19L5 17.6l5.6-5.6L5 6.4L6.4 5l5.6 5.6L17.6 5L19 6.4L13.4 12l5.6 5.6l-1.4 1.4l-5.6-5.6L6.4 19Z" />
            </svg>
        </button>
    </div>

    <div x-data="{ open: false }" x-on:toggle-menu.window="open = !open" x-show="open"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
        class="fixed inset-0 bg-bg bg-opacity-90 z-40 md:hidden">

        <ul class="flex flex-col items-center justify-center h-full space-y-2 uppercase text-black md:text-white">
            <x-nav.nav-link href="#">
                @lang('Rezervēt')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('Dizaina mājas un sauna')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('BUJ')
            </x-nav.nav-link>
            <x-nav.nav-link href="#">
                @lang('Kontakti')
            </x-nav.nav-link>
        </ul>
        <ul class="absolute bottom-12 left-0 right-0 flex justify-center items-center space-x-8">
            <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                <x-social.facebook />
            </x-social.icon>

            <x-social.icon href="https://www.instagram.com/siguldasskati">
                <x-social.instagram />
            </x-social.icon>

            <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                <x-social.tiktok />
            </x-social.icon>
        </ul>
    </div>
</nav>