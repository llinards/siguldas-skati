<header
    class="relative flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-white text-sm py-5 dark:bg-neutral-800">
    <nav class="max-w-7xl w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800"/>
            </a>
            <div class="sm:hidden">
                <button type="button"
                        class="hs-collapse-toggle relative size-9 flex justify-center items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10"
                        id="hs-navbar-example-collapse" aria-expanded="false" aria-controls="hs-navbar-example"
                        aria-label="Toggle navigation" data-hs-collapse="#hs-navbar-example">
                    <svg class="hs-collapse-open:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                         height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                    <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                    <span class="sr-only">Toggle navigation</span>
                </button>
            </div>
        </div>
        <div id="hs-navbar-example"
             class="hidden hs-collapse overflow-hidden transition-all duration-300 basis-full grow sm:block"
             aria-labelledby="hs-navbar-example-collapse">
            <div class="flex flex-col gap-5 mt-5 sm:flex-row sm:items-center sm:justify-end sm:mt-0 sm:ps-5">
                <a class="border-b-1 text-center border-transparent hover:border-b-menu-hover hover:text-menu-hover transition-all
        ease-in-out duration-200 text-btn-md {{ Route::is('dashboard') ? 'border-b-menu-hover text-menu-hover' : '' }}"
                   href="{{route('dashboard')}}">Sākums</a>
                <a class="border-b-1 text-center border-transparent hover:border-b-menu-hover hover:text-menu-hover transition-all
        ease-in-out duration-200 text-btn-md {{ Route::is('profile.edit') ? 'border-b-menu-hover text-menu-hover' : '' }}"
                   href="{{route('profile.edit')}}"> {{ __('Mans Profils') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{route('logout')}}" class="border-b-1 text-center border-transparent hover:border-b-menu-hover hover:text-menu-hover transition-all
        ease-in-out duration-200 text-btn-md"
                       onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                        {{ __('Iziet') }}
                    </a>
                </form>
            </div>
        </div>
    </nav>
</header>
