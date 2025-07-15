<header
    class="relative flex w-full flex-wrap bg-white py-5 text-sm sm:flex-nowrap sm:justify-start dark:bg-neutral-800"
>
    <nav class="mx-auto w-full max-w-7xl px-4 sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800"/>
            </a>
            <div class="sm:hidden">
                <button
                    type="button"
                    class="hs-collapse-toggle relative flex size-9 items-center justify-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:bg-gray-50 focus:outline-hidden disabled:pointer-events-none disabled:opacity-50 dark:border-neutral-700 dark:bg-transparent dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10"
                    id="hs-navbar-example-collapse"
                    aria-expanded="false"
                    aria-controls="hs-navbar-example"
                    aria-label="Toggle navigation"
                    data-hs-collapse="#hs-navbar-example"
                >
                    <svg
                        class="hs-collapse-open:hidden size-4 shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                    <svg
                        class="hs-collapse-open:block hidden size-4 shrink-0"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                    <span class="sr-only">Toggle navigation</span>
                </button>
            </div>
        </div>
        <div
            id="hs-navbar-example"
            class="hs-collapse hidden grow basis-full overflow-hidden transition-all duration-300 sm:block"
            aria-labelledby="hs-navbar-example-collapse"
        >
            <div class="mt-5 flex flex-col gap-5 sm:mt-0 sm:flex-row sm:items-center sm:justify-end sm:ps-5">
                <a
                    class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md {{ Route::is('dashboard') ? 'border-b-menu-hover text-menu-hover' : '' }} border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                    href="{{ route('dashboard') }}"
                >
                    Sākums
                </a>
                <a
                    class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md {{ Route::is('newsletter.list') ? 'border-b-menu-hover text-menu-hover' : '' }} border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                    href="{{ route('newsletter.list') }}"
                >
                    Pieteikumi jaunumiem
                </a>
                <a
                    class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md {{ Route::is('dashboard.features*') ? 'border-b-menu-hover text-menu-hover' : '' }} border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                    href="{{ route('dashboard.features') }}"
                >
                    {{ __('Ērtības') }}
                </a>
                <a
                    class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md {{ Route::is('profile.edit') ? 'border-b-menu-hover text-menu-hover' : '' }} border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                    href="{{ route('profile.edit') }}"
                    wire:navigate
                >
                    {{ __('Mans profils') }}
                </a>
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a
                        hreflang="{{ $localeCode }}"
                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                        class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md {{ app()->getLocale() === $localeCode ? (Route::is('home') ? 'border-b-white' : 'border-b-black') : '' }} border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                    >
                        {{ $properties['native'] }}
                    </a>
                @endforeach

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a
                        href="{{ route('logout') }}"
                        class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md border-b-1 border-transparent text-center transition-all duration-200 ease-in-out"
                        onclick="event.preventDefault();
                                                        this.closest('form').submit();"
                    >
                        {{ __('Iziet') }}
                    </a>
                </form>
            </div>
        </div>
    </nav>
</header>
