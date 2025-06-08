<x-app-layout :title="__('Privātuma politika')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative inline-block mb-3 mt-36">
                <h1 class="text-h-mob lg:text-h-md leading-none">@lang('Privātuma politika')</h1>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray mb-12 leading-none">
                {{ __('Pēdējoreiz atjaunināts: 14.04.2025') }}
            </p>

            <div class="text-base leading-7.5 md:text-xl xl:text-2xl md:leading-10">
                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('1. Personas datu apstrāde')
                </h2>
                <p class="mb-12 lg:mb-12 text-justify">
                    @lang('Mēs apkopojam un apstrādājam jūsu personas datus tikai tad, ja tas ir nepieciešams, lai
                    nodrošinātu mūsu mājaslapas darbību, uzlabotu lietotāja pieredzi un sniegtu jums mūsu pakalpojumus.
                    Mēs veicam šo datu apstrādi, pamatojoties uz likumīgām interesēm, līguma izpildi, tiesisko pienākumu
                    izpildi vai jūsu piekrišanu.')
                </p>

                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('2. Sīkdatnes un to
                    izmantošana')</h2>
                <p class="mb-12 lg:mb-12 text-justify">
                    @lang('Mūsu mājaslapa izmanto sīkdatnes (cookies), lai nodrošinātu labāku lietotāja pieredzi,
                    analizētu vietnes apmeklējumu un veiktu uzlabojumus. Sīkdatnes ir nelielas teksta datnes, kas tiek
                    saglabātas jūsu ierīcē.')
                </p>

                <h3 class="mt-6 lg:mt-0 mb-3 leading-none text-3xl lg:text-5xl">@lang('2.1. Izmantotās sīkdatnes')</h3>
                <div class="overflow-x-auto rounded-lg mb-12 lg:mb-12">
                    <table class="w-full text-left rtl:text-right my-2">
                        <thead class="hidden sm:table-header-group">
                            <tr class="border-y-1 border-storex-inactive-grey text-center">
                                <th class="px-4 py-2">@lang('Sīkdatne')</th>
                                <th class="px-4 py-2">@lang('Mērķis')</th>
                                <th class="px-4 py-2">@lang('Derīguma termiņš')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (Whitecube\LaravelCookieConsent\Facades\Cookies::getCategories() as $category)
                            @foreach ($category->getCookies() as $cookie)
                            <tr class="block sm:table-row border-b-1 border-ss-dark sm:text-left mb-4 sm:mb-0">
                                <td class="block sm:table-cell px-4 py-2">
                                    <span class="font-semibold sm:hidden">@lang('Sīkdatne'): </span>
                                    {{ $cookie->name }}
                                </td>
                                <td class="block sm:table-cell px-4 py-2">
                                    <span class="font-semibold sm:hidden">@lang('Mērķis'): </span>
                                    {{ $cookie->description }}
                                </td>
                                <td class="block sm:table-cell px-4 py-2">
                                    <span class="font-semibold sm:hidden">@lang('Derīguma termiņš'): </span>
                                    {{ \Carbon\CarbonInterval::minutes($cookie->duration)->cascade() }}
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('3. Trešo pušu sīkdatnes')
                </h2>
                <p class="mb-12 lg:mb-12 text-justify">
                    @lang('Papildus mūsu izmantotajām sīkdatnēm mūsu mājaslapa var saturēt arī trešo pušu sīkdatnes,
                    piemēram, analītikas nolūkos vai sociālo tīklu integrācijai. Šādas sīkdatnes var tikt iestatītas,
                    piemēram, Google Analytics vai Facebook.')
                </p>

                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('4. Kā kontrolēt un dzēst
                    sīkdatnes?')</h2>
                <p class="mb-12 lg:mb-12 text-justify">
                    @lang('Jūs varat mainīt savus sīkdatņu iestatījumus, izmantojot pārlūka iestatījumus vai mūsu
                    sīkdatņu pārvaldības paneli. Tomēr, ja jūs izslēdzat noteiktas sīkdatnes, dažas funkcijas mūsu
                    mājaslapā var nedarboties pareizi.')
                </p>

                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('5. Jūsu tiesības saistībā
                    ar personas datiem')</h2>
                <p class="mb-3">@lang('Jums ir tiesības:')</p>
                <div class="product-description mb-12 lg:mb-12">
                    <ul class="list-disc pl-5 text-gray-700">
                        <li>@lang('Piekļūt saviem datiem un saņemt informāciju par to apstrādi')</li>
                        <li>@lang('Pieprasīt labot neprecīzus vai nepilnīgus datus')</li>
                        <li>@lang('Pieprasīt dzēst savus personas datus, ja tie vairs nav nepieciešami')</li>
                        <li>@lang('Ierobežot savu datu apstrādi noteiktos gadījumos')</li>
                        <li>@lang('Saņemt savus personas datus strukturētā formātā un nodot tos citam pakalpojumu
                            sniedzējam')</li>
                        <li>@lang('Iebilst pret datu apstrādi, ja tā tiek veikta uz mūsu leģitīmajām interesēm')</li>
                    </ul>
                </div>

                <h2 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('6. Politikas izmaiņas')
                </h2>
                <p>
                    @lang('Šī privātuma politika var tikt mainīta bez iepriekšēja brīdinājuma. Jaunākā privātuma
                    politikas versija, kas ir publicēta vietnē, aizstāj visas iepriekšējās privātuma politikas
                    versijas.')
                </p>
            </div>
        </div>
    </div>
</x-app-layout>