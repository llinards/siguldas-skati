<div class="bg-ss footer-form" x-data="{ agreed: false }">
    <div class="container mx-auto flex justify-center px-4">
        <div class="my-12 max-w-6xl rounded-3xl bg-white sm:-mb-28 md:mt-18 lg:mt-24 xl:mt-30">
            <div class="items-center p-8 md:p-12 xl:flex">
                <h3
                    class="2xl:text-h-med font-heading mb-4 text-center text-5xl leading-none xl:mb-0 xl:max-w-1/2 xl:px-8 xl:text-6xl"
                >
                    @lang('Uzzini pirmais par jaunumiem!')
                </h3>

                <form class="flex w-full flex-col items-center justify-between xl:px-8" method="post">
                    <label for="email" class="mb-4 block self-start leading-none font-medium text-gray-500">
                        @lang('E-pasts')
                    </label>
                    <div class="w-full items-center sm:flex">
                        <input
                            type="email"
                            id="email"
                            class="border-ss-dark mb-4 block w-full rounded-lg border-1 px-4 py-4 sm:mb-0 xl:ml-0"
                            placeholder="@lang('Ievadiet savu e-pastu')"
                            required
                        />

                        <x-btn-primary
                            type="submit"
                            class="block w-full sm:ml-4 sm:w-auto"
                            x-bind:disabled="!agreed"
                            x-bind:class="!agreed ? 'opacity-50 ' : ''"
                        >
                            @lang('Abonēt')
                        </x-btn-primary>
                    </div>

                    <label class="mt-4 flex cursor-pointer space-x-2 self-start text-sm text-gray-600">
                        <span class="relative">
                            <input
                                type="checkbox"
                                x-model="agreed"
                                class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"
                            />
                            <svg
                                class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                fill="none"
                                viewBox="0 0 20 20"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                            </svg>
                        </span>
                        <span>
                            @lang('Es piekrītu datu uzglabāšanai un apstrādei')
                        </span>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="bg-ss-dark pt-6 pb-6 sm:pt-28 lg:pt-40">
        <div
            class="container mx-auto flex flex-col justify-between px-4 text-white sm:grid sm:grid-cols-2 sm:grid-rows-3 md:grid-cols-4 md:grid-rows-2 lg:flex lg:flex-row"
        >
            <div class="flex flex-col py-6 sm:col-span-2 md:col-span-4 md:items-center md:pt-12 lg:col-span-1 lg:py-0">
                <a href="/{{ app()->getLocale() }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" class="mb-4 w-xs" alt="Siguldas Skati Logo"/>
                </a>
                <p class="text-sm">
                    @lang('© Siguldas skati 2025 | Visas tiesības rezervētas.')
                </p>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="mb-4 text-2xl font-medium">
                        @lang('Izvēlne')
                    </h4>
                    <x-nav.footer-link href="{{ route('products') }}">
                        @lang('Dizaina mājas')
                    </x-nav.footer-link>
                    <x-nav.footer-link href="{{ route('faq') }}">
                        @lang('BUJ')
                    </x-nav.footer-link>
                    <x-nav.footer-link href="{{ route('contacts') }}">
                        @lang('Kontakti')
                    </x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="mb-4 text-2xl font-medium md:text-xl xl:text-2xl">
                        @lang('Informācija')
                    </h4>
                    <x-nav.footer-link href="{{route('privacy-policy')}}">
                        @lang('Privātuma politika')
                    </x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="mb-4 text-2xl font-medium md:text-xl xl:text-2xl">
                        @lang('Kontakti')
                    </h4>
                    <x-nav.footer-link href="tel:+37125666622">+371 25666622</x-nav.footer-link>
                    <x-nav.footer-link href="mailto:info@siguldasskati.lv">info@siguldasskati.lv</x-nav.footer-link>
                    <x-nav.footer-link href="https://maps.app.goo.gl/gEtDt5FS3qpzUtWE9" target="_blank">
                        @lang('Cēsu
                                                            iela 17,
                                                            Sigulda, Latvija')
                    </x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <h4 class="mb-4 text-2xl font-bold md:text-xl xl:text-2xl">
                    @lang('Pieseko')
                </h4>
                <ul class="flex flex-row gap-4">
                    <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                        <x-social.facebook class="transition-colors duration-200 hover:text-gray-500"/>
                    </x-social.icon>

                    <x-social.icon href="https://www.instagram.com/siguldasskati">
                        <x-social.instagram class="transition-colors duration-200 hover:text-gray-500"/>
                    </x-social.icon>

                    <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                        <x-social.tiktok class="transition-colors duration-200 hover:text-gray-500"/>
                    </x-social.icon>
                </ul>
            </div>
        </div>
        <div class="container mx-auto my-6 px-4 text-sm text-white md:text-center lg:mt-12">
            @lang('Mājaslapu izstrādāja')
            <a
                href="https://slmedia.lv"
                class="hover:text-ss-gray underline transition-all duration-200"
                target="_blank"
                rel="noopener"
            >
                S&amp;L Media SIA
            </a>
        </div>
    </div>
</footer>
