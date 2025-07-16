<nav class="absolute z-50 w-full" x-data="handleMobileMenu()" x-init="init()">
    <div class="relative z-50 container mx-auto flex items-center justify-between px-4 py-8">
        <a
            href="/{{ app()->getLocale() }}"
            class="{{ Route::is('home') ? 'focus-visible:outline-white' : 'focus-visible:outline-ss-dark' }} flex items-center focus-visible:outline! focus-visible:outline-offset-6!"
        >
            <img
                src="{{ asset('images/logo.png') }}"
                class="{{ Route::is('home') ? '' : 'invert' }} w-44 transition-all duration-300 lg:w-56 xl:w-82"
                :class="open ? 'invert' : ''"
                alt="Siguldas Skati Logo"
            />
        </a>
        <ul class="{{ Route::is('home') ? 'text-white' : 'text-black' }} hidden uppercase lg:flex">
            {{--            <x-nav.nav-link href="#" class="{{ Route::is('book') ? 'border-b-ss-gray text-ss-gray' : '' }}">--}}
            {{--                @lang('Rezervēt')--}}
            {{--            </x-nav.nav-link>--}}
            <x-nav.nav-link
                href="{{ route('products') }}"
                class="{{ Route::is('products') ? 'border-b-ss-gray text-ss-gray' : '' }}"
            >
                @lang('Dizaina mājas un sauna')
            </x-nav.nav-link>
            <x-nav.nav-link
                href="{{ route('faq') }}"
                class="{{ Route::is('faq') ? 'border-b-ss-gray text-ss-gray' : '' }}"
            >
                @lang('BUJ')
            </x-nav.nav-link>
            <x-nav.nav-link
                href="{{ route('contacts') }}"
                class="{{ Route::is('contacts') ? 'border-b-ss-gray text-ss-gray' : '' }}"
            >
                @lang('Kontakti')
            </x-nav.nav-link>

            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <x-nav.nav-link
                    rel="alternate"
                    hreflang="{{ $localeCode }}"
                    href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                    class="{{ app()->getLocale() === $localeCode ? (Route::is('home') ? 'border-b-white' : 'border-b-black') : '' }}"
                >
                    {{ $properties['native'] }}
                </x-nav.nav-link>
            @endforeach
        </ul>

        <button
            x-on:click="open = !open"
            class="{{ Route::is('home') ? 'focus-visible:outline-white' : 'focus-visible:outline-ss-dark' }} relative z-50 focus-visible:outline! lg:hidden"
            :class="open ? 'focus-visible:outline-ss-dark' : 'focus-visible:outline-ss-dark'"
        >
            <svg
                x-show="!open"
                class="{{ Route::is('home') ? 'fill-white' : 'fill-black' }} size-12 cursor-pointer"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
            >
                <path
                    fill-rule="evenodd"
                    d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                x-show="open"
                class="hover:fill-menu-hover size-12 duration-200"
                xmlns="http://www.w3.org/2000/svg"
                fill="black"
                viewBox="0 0 24 24"
            >
                <path
                    d="M6.4 19L5 17.6l5.6-5.6L5 6.4L6.4 5l5.6 5.6L17.6 5L19 6.4L13.4 12l5.6 5.6l-1.4 1.4l-5.6-5.6L6.4 19Z"
                />
            </svg>
        </button>
    </div>

    <div
        x-show="open"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition duration-300 ease-in"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="bg-ss bg-opacity-90 fixed inset-0 z-40 lg:hidden"
    >
        <ul class="flex h-full flex-col items-center justify-center space-y-2 text-black uppercase lg:text-white">
            {{--            <x-nav.nav-link href="#" class="{{ Route::is('book') ? 'border-b-ss-gray text-ss-gray' : '' }}">--}}
            {{--                @lang('Rezervēt')--}}
            {{--            </x-nav.nav-link>--}}
            <x-nav.nav-link
                href="{{ route('products') }}"
                class="{{ Route::is('products') ? 'border-b-ss-gray text-ss-gray' : '' }}"
            >
                @lang('Dizaina mājas un sauna')
            </x-nav.nav-link>
            <x-nav.nav-link href="/buj" class="{{ Route::is('faq') ? 'border-b-ss-gray text-ss-gray' : '' }}">
                @lang('BUJ')
            </x-nav.nav-link>
            <x-nav.nav-link
                href="{{ route('contacts') }}"
                class="{{ Route::is('contacts') ? 'border-b-ss-gray text-ss-gray' : '' }}"
            >
                @lang('Kontakti')
            </x-nav.nav-link>
            <ul class="flex">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <x-nav.nav-link
                        rel="alternate"
                        hreflang="{{ $localeCode }}"
                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                        :class="app()->getLocale() === $localeCode ? ' border-b-black' : ''"
                    >
                        {{ $properties['native'] }}
                    </x-nav.nav-link>
                @endforeach
            </ul>
        </ul>
        <ul class="absolute right-0 bottom-12 left-0 flex items-center justify-center space-x-8">
            <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                <x-social.facebook/>
            </x-social.icon>

            <x-social.icon href="https://www.instagram.com/siguldasskati">
                <x-social.instagram/>
            </x-social.icon>

            <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                <x-social.tiktok/>
            </x-social.icon>
        </ul>
    </div>
</nav>
