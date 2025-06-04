<x-app-layout :title="__('Privātuma politika')">
    <div class="container mx-auto px-4 pb-8 sm:py-12 lg:px-6 xl:px-8">
        <div class="pb-8 pt-28 sm:pb-12 sm:pt-0">
            <h1>@lang('Privātuma politika')</h1>
            <p class="mt-2 text-gray-600">{{ __('Pēdējoreiz atjaunināts: 14.04.2025') }}</p>
        </div>

        <div class="prose lg:prose-lg mx-auto">
            <h2 class="text-xl">@lang('1. Personas datu apstrāde')</h2>
            <p class="mb-2">
                @lang('Mēs apkopojam un apstrādājam jūsu personas datus tikai tad, ja tas ir nepieciešams, lai nodrošinātu mūsu mājaslapas darbību, uzlabotu lietotāja pieredzi un sniegtu jums mūsu pakalpojumus. Mēs veicam šo datu apstrādi, pamatojoties uz likumīgām interesēm, līguma izpildi, tiesisko pienākumu izpildi vai jūsu piekrišanu.')
            </p>

            <h2 class="text-xl">@lang('2. Sīkdatnes un to izmantošana')</h2>
            <p class="mb-2">
                @lang('Mūsu mājaslapa izmanto sīkdatnes (cookies), lai nodrošinātu labāku lietotāja pieredzi, analizētu vietnes apmeklējumu un veiktu uzlabojumus. Sīkdatnes ir nelielas teksta datnes, kas tiek saglabātas jūsu ierīcē.')
            </p>

            <h3 class="text-lg">@lang('2.1. Izmantotās sīkdatnes')</h3>
            <div class="overflow-x-auto rounded-lg">
                <table class="w-full text-left rtl:text-right my-2">
                    <thead>
                    <tr class="border-y-1 border-storex-inactive-grey text-center">
                        <th class="px-4 py-2">@lang('Sīkdatne')</th>
                        <th class="px-4 py-2">@lang('Mērķis')</th>
                        <th class="px-4 py-2">@lang('Derīguma termiņš')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach (Whitecube\LaravelCookieConsent\Facades\Cookies::getCategories() as $category)
                        @foreach ($category->getCookies() as $cookie)
                            <tr class="border-b-1 border-storex-grey text-center">
                                <td class="">{{ $cookie->name }}</td>
                                <td class="">{{ $cookie->description }}</td>
                                <td class="">
                                    {{ \Carbon\CarbonInterval::minutes($cookie->duration)->cascade() }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>

            <h2 class="text-xl">@lang('3. Trešo pušu sīkdatnes')</h2>
            <p class="mb-2">
                @lang('Papildus mūsu izmantotajām sīkdatnēm mūsu mājaslapa var saturēt arī trešo pušu sīkdatnes, piemēram, analītikas nolūkos vai sociālo tīklu integrācijai. Šādas sīkdatnes var tikt iestatītas, piemēram, Google Analytics vai Facebook.')
            </p>

            <h2 class="text-xl">@lang('4. Kā kontrolēt un dzēst sīkdatnes?')</h2>
            <p class="mb-2">
                @lang('Jūs varat mainīt savus sīkdatņu iestatījumus, izmantojot pārlūka iestatījumus vai mūsu sīkdatņu pārvaldības paneli. Tomēr, ja jūs izslēdzat noteiktas sīkdatnes, dažas funkcijas mūsu mājaslapā var nedarboties pareizi.')
            </p>

            <h2 class="text-xl">@lang('5. Jūsu tiesības saistībā ar personas datiem')</h2>
            <p class="mb-2">@lang('Jums ir tiesības:')</p>
            <div class="product-description">
                <ul class="list-disc pl-5 text-gray-700">
                    <li>@lang('Piekļūt saviem datiem un saņemt informāciju par to apstrādi')</li>
                    <li>@lang('Pieprasīt labot neprecīzus vai nepilnīgus datus')</li>
                    <li>@lang('Pieprasīt dzēst savus personas datus, ja tie vairs nav nepieciešami')</li>
                    <li>@lang('Ierobežot savu datu apstrādi noteiktos gadījumos')</li>
                    <li>@lang('Saņemt savus personas datus strukturētā formātā un nodot tos citam pakalpojumu sniedzējam')</li>
                    <li>@lang('Iebilst pret datu apstrādi, ja tā tiek veikta uz mūsu leģitīmajām interesēm')</li>
                </ul>
            </div>

            <h2 class="text-xl">@lang('6. Politikas izmaiņas')</h2>
            <p class="mb-2">
                @lang('Šī privātuma politika var tikt mainīta bez iepriekšēja brīdinājuma. Jaunākā privātuma politikas versija, kas ir publicēta vietnē, aizstāj visas iepriekšējās privātuma politikas versijas.')
            </p>
        </div>
    </div>
</x-app-layout>
