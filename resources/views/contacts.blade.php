<x-app-layout :title="__('Kontakti')">

    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative inline-block mb-3 mt-36">
                <h1 class="text-h-mob lg:text-h-md leading-none">@lang('Sazinies ar mums')</h1>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray pb-6 leading-none lg:max-w-1/3">
                @lang('Sazinies ar mums, ja rodas neskaidrības vai vēlies uzzināt vairāk par mūsu brīvdienu dizaina
                mājām')!</p>

            <div class="lg:grid grid-cols-2 xl:grid-cols-3 gap-x-6 lg:gap-x-12 items-center">
                <div class="col-span-1 xl:col-span-2 h-full space-y-6 lg:space-y-8">
                    <div class="h-96 lg:h-2/3">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2163.568745360419!2d24.84604!3d57.161528!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46e94fcd4be121c3%3A0x978c8c05100f4580!2sC%C4%93su%20iela%2017%2C%20Sigulda%2C%20Siguldas%20pils%C4%93ta%2C%20Siguldas%20novads%2C%20LV-2150!5e0!3m2!1sen!2slv!4v1750959826216!5m2!1sen!2slv"
                            class="h-full w-full" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div class="text-center sm:text-left sm:grid grid-cols-2">
                        <div class="mb-6 lg:mb-0 space-y-6 lg:space-y-8">
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
                        <div class="mb-6 lg:mb-0 space-y-6 lg:space-y-8">
                            <ul>
                                <li>
                                    <h2>@lang('Adrese')</h2>
                                </li>
                                <li>
                                    <x-link href="https://maps.app.goo.gl/gEtDt5FS3qpzUtWE9" target="_blank">Cēsu iela
                                        17,
                                        Sigulda, Latvija</x-link>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <h2>@lang('Pieseko')</h2>
                                </li>
                                <li>
                                    <ul class="flex justify-center sm:justify-start space-x-3">
                                        <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                                            <x-social.facebook
                                                class="hover:text-gray-500 transition-colors duration-200" />
                                        </x-social.icon>

                                        <x-social.icon href="https://www.instagram.com/siguldasskati">
                                            <x-social.instagram
                                                class="hover:text-gray-500 transition-colors duration-200" />
                                        </x-social.icon>

                                        <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                                            <x-social.tiktok
                                                class="hover:text-gray-500 transition-colors duration-200" />
                                        </x-social.icon>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="md:flex lg:flex-none items-center col-span-1 justify-center">
                    <form action="/" method="POST" class="md:w-4/5 lg:w-full" x-data="{ agreed: false }">
                        <div
                            class="flex flex-col p-6 space-y-6 mb-6 border-1 border-ss-gray text-ss-gray rounded-3xl shadow-md">
                            <label for="firstName">@lang('Vārds') *</label>
                            <input class="border-b" id="firstName" type="text" required>
                            <label for="lastName">@lang('Uzvārds') *</label>
                            <input class="border-b" id="lastName" type="text" required>
                            <label for="email">@lang('E-pasts') *</label>
                            <input class="border-b" id="email" type="email" required>
                            <label for="question">@lang('Jautājums') *</label>
                            <textarea class="border-b w-full resize-none" rows="5" cols="30" id="question"
                                required></textarea>
                            <label class="mt-4 self-start flex text-sm text-gray-600 space-x-2 cursor-pointer">
                                <span class="relative">
                                    <input type="checkbox" x-model="agreed"
                                        class="peer appearance-none h-5 w-5 border-1 border-ss-dark rounded bg-ss checked:bg-ss-dark checked:border-ss-dark transition duration-200">
                                    <svg class="pointer-events-none absolute left-0 top-0 h-5 w-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-150"
                                        fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
                                    </svg>
                                </span>
                                <span>@lang('Es piekrītu datu uzglabāšanai un apstrādei')</span>
                            </label>
                        </div>

                        <div class="flex w-full">
                            <x-btn-primary type="submit" class="w-full" x-bind:disabled="!agreed"
                                x-bind:class="!agreed ? 'opacity-50 ' : ''">@lang('Nosūtīt')</x-btn-primary>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>