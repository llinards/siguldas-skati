<x-app-layout :title="__('Noteikumi')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <div class="flex sm:inline-block justify-center items-start border-b-2">
                    <x-btn-back class="pb-3 mr-5"/>
                    <h1 class="text-h-mob lg:text-h-md leading-none">
                        {{-- prettier-ignore --}}
                        @lang('Iekšējās kārtības noteikumi')
                    </h1>
                </div>
            </div>
            <div class="text-base leading-7.5 md:text-xl md:leading-10 xl:text-2xl">
                <div class="mb-8">
                    <p class="my-6 text-justify">
                        @lang('Mēs priecājamies Jūs uzņemt Siguldas Skatos un lūdzam ievērot šos noteikumus, lai Jūsu un citu viesu atpūta būtu patīkama un droša!')
                    </p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('1. Vispārīgie noteikumi')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('1.1. Reģistrēšanās no plkst. 15:00, izrakstīšanās līdz plkst. 11:00.')</p>
                    <p class="mb-2">@lang('1.2. Nakšņot drīkst tikai personas, kas ir reģistrētas rezervācijā.')</p>
                    <p class="mb-2">@lang('1.3. Teritorijā jāievēro cieņa pret citiem viesiem un apkārtējo vidi.')</p>
                    <p class="mb-2">@lang('1.4. Lūdzam brīvdienu mājās nepārvietoties ar āra apaviem.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('2. Uzvedība un miers')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('2.1. Naktsmiers no plkst. 23:00 līdz 07:00.')</p>
                    <p class="mb-2">@lang('2.2. Skaļa mūzika un troksnis nav pieļaujami, ja tie traucē citiem viesiem vai kaimiņiem.')</p>
                    <p class="mb-2">@lang('2.3. Bērni drošas vides uzturēšanās laikā atrodas vecāku vai aizbildņu atbildībā.')</p>
                    <p class="mb-2">@lang('2.4. Viesiem jāievēro cieņa pret citiem apmeklētājiem, darbiniekiem un apkārtējiem kaimiņiem.')</p>
                    <p class="mb-2">@lang('2.5. Viesiem jābūt pilngadīgiem, vai arī kopā ar pilngadīgu pavadīšo personu.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('3. Īpašuma un aprīkojuma izmantošana')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('3.1. Lūdzam saudzīgi izturēties pret inventāru, mēbelēm, sadzīves tehniku un telpām.')</p>
                    <p class="mb-2">@lang('3.2. Par bojājumiem vai zaudējumiem viesis sedz atbildību naudā atbilstoši vērtībai.')</p>
                    <p class="mb-2">@lang('3.3. Aizliegts pārvietot mēbeles vai aprīkojumu bez saskaņošanas ar administrāciju.')</p>
                    <p class="mb-2">@lang('3.4. Ja tiek zaudēta atslēga vai tālvadības pults, jāmaksā nomaiņas izmaksas (100€).')</p>
                    <p class="mb-2">@lang('3.5. Par visiem bojājumiem un zaudējumiem jāinformē saimnieks, nevis jācenšas tos slēpt.')</p>
                    <p class="mb-2">@lang('3.6. Pārvietošanās pa teritoriju atļauta tikai tam paredzētajās vietās, nevis pa zaļajām zonām.')</p>
                    <p class="mb-2">@lang('3.7. Lūdzam neaiztikt un nepārvietot zāliena pļaušanas i-robotu.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('4. Drošība')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('4.1. Smēķēšana telpās ir stingri aizliegta (atļauta tikai norādītās vietās ārā).')</p>
                    <p class="mb-2">@lang('4.2. Par viesu drošību un īpašuma aizsardzību teritorijā var darboties videonovērošana.')</p>
                    <p class="mb-2">@lang('4.3. Saimnieki nav atbildīgi par viesu personīgajām mantām.')</p>
                    <p class="mb-2">@lang('4.4. Ugunsgrēka gadījumā nekavējoties zvanīt 112 un informēt saimniekus.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('5. Mājdzīvnieki')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('5.1. Mājdzīvnieki atļauti tikai pēc iepriekšējas saskaņošanas.')</p>
                    <p class="mb-2">@lang('5.2. Mājdzīvnieki nedrīkst traucēt citus viesus vai bojāt īpašumu.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('6. Vide un atkritumi')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('6.1. Atkritumi jāizmet tam paredzētajos konteineros.')</p>
                    <p class="mb-2">@lang('6.2. Lūdzam taupīt resursus.')</p>
                    <p class="mb-2">@lang('6.3. Neatstājiet atkritumus dabā vai uz terasēm.')</p>
                    <p class="mb-2">@lang('6.4. Saglabāsim vidi tīru un sakoptu – lai mums visiem ir patīkami uzturēties.')</p>
                    <p class="mb-2">@lang('6.5. Transportlīdzekļi jānovieto tikai tam paredzētajās vietās.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('7. Atbildība un sods')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('7.1. Ja tiek pārkāpti noteikumi, administrācija ir tiesīga izraidīt bez naudas atmaksas.')</p>
                    <p class="mb-2">@lang('7.2. Viesi atbild par zaudējumiem, kas nodarīti īpašumam vai aprīkojumam.')</p>
                    <p class="mb-2">@lang('7.3. Ugunsdrošības instrukcijas ir jāizpilda un jāievēro noteiktie kārtības noteikumi.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none text-red-600">
                    @lang('Aizliegtas lietas')
                </h2>
                <div class="text-justify">
                    <p class="mb-2">@lang('– Narkotiku, nelikumīgu vielu vai pārmērīga alkohola lietošana.')</p>
                    <p>@lang('– Ieroču, bīstamu vielu vai sprāgstvielu atrašanās un/vai lietošana teritorijā.')</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
