<x-app-layout :title="__('Privātuma politika')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <x-btn-back class="pb-3" />
                <h1 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Privātuma politika')
                </h1>

            </div>
            <p class="text-ss-gray mb-12 text-sm leading-none">
                {{ __('Pēdējoreiz atjaunināts: 14.04.2025') }}
            </p>

            <div class="text-base leading-7.5 md:text-xl md:leading-10 xl:text-2xl">
                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('1. Personas datu apstrāde')
                </h2>
                <p class="mb-12 text-justify lg:mb-12">
                    @lang('Mēs apkopojam un apstrādājam jūsu personas datus tikai tad, ja tas ir nepieciešams, lai
                    nodrošinātu mūsu mājaslapas darbību, uzlabotu lietotāja pieredzi un sniegtu jums mūsu pakalpojumus.
                    Mēs veicam šo datu apstrādi, pamatojoties uz likumīgām interesēm, līguma izpildi, tiesisko pienākumu
                    izpildi vai jūsu piekrišanu.')
                </p>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('2. Sīkdatnes un to
                    izmantošana')
                </h2>
                <p class="mb-12 text-justify lg:mb-12">
                    @lang('Mūsu mājaslapa izmanto sīkdatnes (cookies), lai nodrošinātu labāku lietotāja pieredzi,
                    analizētu vietnes apmeklējumu un veiktu uzlabojumus. Sīkdatnes ir nelielas teksta datnes, kas tiek
                    saglabātas jūsu ierīcē.')
                </p>

                <h3 class="mt-6 mb-3 text-3xl leading-none lg:mt-0 lg:text-5xl">
                    @lang('2.1. Izmantotās sīkdatnes')
                </h3>
                <div class="mb-12 overflow-x-auto rounded-lg lg:mb-12">
                    <table class="my-2 w-full text-left rtl:text-right">
                        <thead class="hidden sm:table-header-group">
                            <tr class="border-storex-inactive-grey border-y-1 text-center">
                                <th class="px-4 py-2">
                                    @lang('Sīkdatne')
                                </th>
                                <th class="px-4 py-2">@lang('Mērķis')</th>
                                <th class="px-4 py-2">
                                    @lang('Derīguma termiņš')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (Whitecube\LaravelCookieConsent\Facades\Cookies::getCategories() as $category)
                            @foreach ($category->getCookies() as $cookie)
                            <tr class="border-ss-dark mb-4 block border-b-1 sm:mb-0 sm:table-row sm:text-left">
                                <td class="block px-4 py-2 sm:table-cell">
                                    <span class="font-semibold sm:hidden">
                                        @lang('Sīkdatne')
                                        :
                                    </span>
                                    {{ $cookie->name }}
                                </td>
                                <td class="block px-4 py-2 sm:table-cell">
                                    <span class="font-semibold sm:hidden">
                                        @lang('Mērķis')
                                        :
                                    </span>
                                    {{ $cookie->description }}
                                </td>
                                <td class="block px-4 py-2 sm:table-cell">
                                    <span class="font-semibold sm:hidden">
                                        @lang('Derīguma termiņš')
                                        :
                                    </span>
                                    {{ \Carbon\CarbonInterval::minutes($cookie->duration)->cascade() }}
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('3. Trešo pušu sīkdatnes')
                </h2>
                <p class="mb-12 text-justify lg:mb-12">
                    @lang('Papildus mūsu izmantotajām sīkdatnēm mūsu mājaslapa var saturēt arī trešo pušu sīkdatnes,
                    piemēram, analītikas nolūkos vai sociālo tīklu integrācijai. Šādas sīkdatnes var tikt iestatītas,
                    piemēram, Google Analytics vai Facebook.')
                </p>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('4. Kā kontrolēt un dzēst
                    sīkdatnes?')
                </h2>
                <p class="mb-12 text-justify lg:mb-12">
                    @lang('Jūs varat mainīt savus sīkdatņu iestatījumus, izmantojot pārlūka iestatījumus vai mūsu
                    sīkdatņu pārvaldības paneli. Tomēr, ja jūs izslēdzat noteiktas sīkdatnes, dažas funkcijas mūsu
                    mājaslapā var nedarboties pareizi.')
                </p>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('5. Jūsu tiesības saistībā
                    ar personas datiem')
                </h2>
                <p class="mb-3">@lang('Jums ir tiesības:')</p>
                <div class="product-description mb-12 lg:mb-12">
                    <ul class="list-disc pl-5 text-gray-700">
                        <li>
                            @lang('Piekļūt saviem datiem un saņemt informāciju par to apstrādi')
                        </li>
                        <li>
                            @lang('Pieprasīt labot neprecīzus vai nepilnīgus datus')
                        </li>
                        <li>
                            @lang('Pieprasīt dzēst savus personas datus, ja tie vairs nav nepieciešami')
                        </li>
                        <li>
                            @lang('Ierobežot savu datu apstrādi noteiktos gadījumos')
                        </li>
                        <li>
                            @lang('Saņemt savus personas datus strukturētā formātā un nodot tos citam pakalpojumu
                            sniedzējam')
                        </li>
                        <li>
                            @lang('Iebilst pret datu apstrādi, ja tā tiek veikta uz mūsu leģitīmajām interesēm')
                        </li>
                    </ul>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                    @lang('6. Politikas izmaiņas')
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