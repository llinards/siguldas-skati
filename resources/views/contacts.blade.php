<x-app-layout :title="__('Kontakti')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-36 mb-3 inline-block">
                <h1 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Sazinies ar mums')
                </h1>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none lg:max-w-1/3">
                {{-- prettier-ignore --}}
                @lang('Sazinies ar mums, ja rodas neskaidrības vai vēlies uzzināt vairāk par mūsu brīvdienu dizaina
                mājām.')
            </p>

            <div class="grid-cols-2 items-center gap-x-6 lg:grid lg:gap-x-12 xl:grid-cols-3">
                <div class="col-span-1 h-full space-y-6 lg:space-y-8 xl:col-span-2">
                    <div class="h-96 lg:h-2/3">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2163.568745360419!2d24.84604!3d57.161528!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46e94fcd4be121c3%3A0x978c8c05100f4580!2sC%C4%93su%20iela%2017%2C%20Sigulda%2C%20Siguldas%20pils%C4%93ta%2C%20Siguldas%20novads%2C%20LV-2150!5e0!3m2!1sen!2slv!4v1750959826216!5m2!1sen!2slv"
                            class="h-full w-full" style="border: 0" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="grid-cols-2 text-center sm:grid sm:text-left">
                        <div class="mb-6 space-y-6 lg:mb-0 lg:space-y-8">
                            <ul>
                                <li>
                                    <h2>@lang('Telefons')</h2>
                                </li>
                                <li>
                                    <x-link href="tel:+37125666622" class="">+371 25666622</x-link>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <h2>@lang('E-pasts')</h2>
                                </li>
                                <li>
                                    <x-link href="mailto:info@siguldasskati.lv" class="">info@siguldasskati.lv</x-link>
                                </li>
                            </ul>
                        </div>
                        <div class="mb-6 space-y-6 lg:mb-0 lg:space-y-8">
                            <ul>
                                <li>
                                    <h2>@lang('Adrese')</h2>
                                </li>
                                <li>
                                    <x-link href="https://maps.app.goo.gl/gEtDt5FS3qpzUtWE9" target="_blank">
                                        @lang('Cēsu iela 17, Sigulda, Latvija')
                                    </x-link>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <h2>@lang('Pieseko')</h2>
                                </li>
                                <li>
                                    <ul class="flex justify-center space-x-3 sm:justify-start">
                                        <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                                            <x-social.facebook
                                                class="transition-colors duration-200 hover:text-gray-500" />
                                        </x-social.icon>

                                        <x-social.icon href="https://www.instagram.com/siguldasskati">
                                            <x-social.instagram
                                                class="transition-colors duration-200 hover:text-gray-500" />
                                        </x-social.icon>

                                        <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                                            <x-social.tiktok
                                                class="transition-colors duration-200 hover:text-gray-500" />
                                        </x-social.icon>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 items-center justify-center md:flex lg:flex-none">
                    <livewire:contact-us />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>