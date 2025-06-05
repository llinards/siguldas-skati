<div class="bg-ss" x-data="{ agreed: false }">
    <div class="px-4 container mx-auto flex justify-center">
        <div class="mb-12 sm:-mb-28 max-w-6xl bg-white rounded-3xl">
            <div class="xl:flex p-8 md:p-12 items-center">
                <h3
                    class="xl:max-w-1/2 xl:px-8 text-5xl xl:text-6xl 2xl:text-h-med font-heading leading-none text-center mb-4 xl:mb-0">
                    @lang('Uzzini pirmais par jaunumiem!')
                </h3>

                <form class="w-full flex justify-between flex-col items-center xl:px-8" method="post">
                    <label for="email"
                           class="text-gray-500 block font-medium mb-4 self-start leading-none">@lang('E-pasts')</label>
                    <div class="sm:flex items-center w-full">
                        <input type="email" id="email"
                               class="mb-4 sm:mb-0 py-4 px-4 xl:ml-0 block border-1 rounded-lg border-ss-dark w-full"
                               placeholder="@lang('Ievadiet savu e-pastu')" required>

                        <x-btn-primary type="submit" class="w-full sm:w-auto block sm:ml-4" x-bind:disabled="!agreed"
                                       x-bind:class="!agreed ? 'opacity-50 ' : 'cursor-pointer hover:bg-white hover:border-black hover:text-black'">
                            @lang('Abonēt')
                        </x-btn-primary>
                    </div>

                    <label class="mt-4 self-start flex text-sm text-gray-600 space-x-2 cursor-pointer">
                        <span class="relative">
                            <input type="checkbox" x-model="agreed"
                                   class="peer appearance-none h-5 w-5 border-1 border-ss-dark rounded bg-white checked:bg-ss-dark checked:border-ss-dark transition duration-200">
                            <svg
                                class="pointer-events-none absolute left-0 top-0 h-5 w-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-150"
                                fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                            </svg>
                        </span>
                        <span>@lang('Es piekrītu datu uzglabāšanai un apstrādei')</span>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>


<footer>
    <div class="bg-ss-dark pb-6 pt-6 sm:pt-28 lg:pt-40">
        <div
            class="px-4 container mx-auto flex flex-col sm:grid sm:grid-cols-2 sm:grid-rows-3 md:grid-cols-4 md:grid-rows-2 lg:flex lg:flex-row text-white justify-between">
            <div class="flex flex-col sm:col-span-2 md:col-span-4 lg:col-span-1 md:items-center py-6 md:pt-12 lg:py-0">
                <a href="/{{ app()->getLocale() }}" class="flex items-center">
                    <img src="{{ asset('images/siguldas-skati-logo.png') }}" class="w-xs mb-4"
                         alt="Siguldas Skati Logo"/>
                </a>
                <p class="text-sm">@lang('© Siguldas skati 2025 | Visas tiesības rezervētas.')</p>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="text-2xl font-medium mb-4">@lang('Izvēlne')</h4>
                    <x-nav.footer-link href="#">@lang('Dizaina mājas')</x-nav.footer-link>
                    <x-nav.footer-link href="#">@lang('BUJ')</x-nav.footer-link>
                    <x-nav.footer-link href="#">@lang('Kontakti')</x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="text-2xl md:text-xl xl:text-2xl font-medium mb-4">@lang('Informācija')</h4>
                    <x-nav.footer-link
                        href="{{route('privacy-policy')}}">@lang('Privātuma politika')</x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <ul>
                    <h4 class="text-2xl md:text-xl xl:text-2xl font-medium mb-4">@lang('Kontakti')</h4>
                    <x-nav.footer-link href="tel:+37125666622">+371 25666622</x-nav.footer-link>
                    <x-nav.footer-link href="mailto:info@siguldasskati.lv">info@siguldasskati.lv
                    </x-nav.footer-link>
                    <x-nav.footer-link href="https://maps.app.goo.gl/gEtDt5FS3qpzUtWE9" target="_blank">@lang('Cēsu
                        iela 17,
                        Sigulda, Latvija')</x-nav.footer-link>
                </ul>
            </div>
            <div class="mb-6 lg:my-0">
                <h4 class="text-2xl md:text-xl xl:text-2xl font-bold mb-4">@lang('Pieseko')</h4>
                <ul class="flex flex-row gap-4">
                    <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                        <x-social.facebook class="hover:text-gray-500 transition-colors duration-200"/>
                    </x-social.icon>

                    <x-social.icon href="https://www.instagram.com/siguldasskati">
                        <x-social.instagram class="hover:text-gray-500 transition-colors duration-200"/>
                    </x-social.icon>

                    <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                        <x-social.tiktok class="hover:text-gray-500 transition-colors duration-200"/>
                    </x-social.icon>
                </ul>

            </div>
        </div>
        <div class="text-sm px-4 container mx-auto text-white md:text-center my-6 lg:mt-12">
            @lang('Mājaslapu izstrādāja')
            <a href="https://slmedia.lv" class="underline hover:text-ss-gray transition-all duration-200"
               target="_blank" rel="noopener">
                S&amp;L Media SIA
            </a>
        </div>
    </div>

</footer>
