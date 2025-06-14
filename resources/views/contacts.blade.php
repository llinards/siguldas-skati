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

            <div class="md:grid grid-cols-2 lg:grid-cols-3 items-center">
                <div class="mb-6 lg:mb-0 space-y-6 lg:space-y-12">
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
                <div class="mb-6 lg:mb-0 space-y-6 lg:space-y-12">
                    <ul>
                        <li>
                            <h2>@lang('Adrese')</h2>
                        </li>
                        <li>
                            <x-link href="https://maps.app.goo.gl/gEtDt5FS3qpzUtWE9" target="_blank">Cēsu iela 17,
                                Sigulda, Latvija</x-link>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <h2>@lang('Pieseko')</h2>
                        </li>
                        <li>
                            <ul class="flex space-x-3">
                                <x-social.icon href="https://www.facebook.com/ModernHouseLV">
                                    <x-social.facebook class="hover:text-gray-500 transition-colors duration-200" />
                                </x-social.icon>

                                <x-social.icon href="https://www.instagram.com/siguldasskati">
                                    <x-social.instagram class="hover:text-gray-500 transition-colors duration-200" />
                                </x-social.icon>

                                <x-social.icon href="https://www.tiktok.com/@modernhouse_lv">
                                    <x-social.tiktok class="hover:text-gray-500 transition-colors duration-200" />
                                </x-social.icon>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-span-3 lg:col-span-1">
                    <form action="/" method="POST">
                        <div
                            class="flex flex-col p-6 space-y-6 mb-6 border-1 border-ss-gray text-ss-gray rounded-3xl shadow-md">
                            <label for="firstName">@lang('Vārds') *</label>
                            <input class="border-b" id="firstName" type="text" required>
                            <label for="lastName">@lang('Uzvārds') *</label>
                            <input class="border-b" id="lastName" type="text" required>
                            <label for="email">@lang('E-pasts') *</label>
                            <input class="border-b" id="email" type="email" required>
                            <label for="question">
                                <textarea class="border-b w-full resize-none" rows="5" cols="30" id="question"
                                    required>@lang('Jautājums') *</textarea>
                        </div>

                        <div class="flex w-full">
                            <x-btn-primary type="submit" class="w-full">@lang('Nosūtīt')</x-btn-primary>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>