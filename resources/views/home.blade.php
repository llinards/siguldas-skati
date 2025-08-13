<x-app-layout :title="__('Sākums')">
    {{-- HEADER --}}
    <div class="home-introduction relative flex justify-center bg-black overflow-hidden">
        <!-- Carousel background -->
        <div class="absolute inset-0 z-0">
            <div class="f-carousel" id="header-carousel">
                @foreach (range(1, 7) as $index)
                    <div class="f-carousel__slide relative w-full">
                        <div class="absolute inset-0 bg-black/50 z-10"></div>
                        <img src="{{ asset('images/header-image' . $index . '.jpg') }}" 
                             class="w-full h-screen object-cover" 
                             alt="Header Image {{ $index }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Foreground content -->
        <div class="container relative z-10 mx-auto flex flex-col items-center px-4">
            <h1
                class="text-h-mob xs:text-6xl xl:text-h font-heading absolute top-48 z-10 max-w-7xl text-center leading-12 text-white uppercase sm:top-1/2 sm:-translate-y-1/2 sm:text-7xl sm:leading-16 md:text-8xl md:leading-24 xl:leading-28">
                @lang('Modernas brīvdienu dizaina mājas tavai atpūtai!')
            </h1>

            <x-btn-header href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html"
                        class="absolute bottom-48 z-10 sm:bottom-16">
                @lang('Rezervēt')
            </x-btn-header>
        </div>
    </div>

    {{-- ABOUT US --}}
    <div id="about-us" class="bg-ss">
        <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
            <div>
                <div class="relative mb-3 inline-block">
                    <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                        @lang('Par mums')
                    </h2>
                </div>
                <p class="text-ss-gray pb-6 text-sm leading-none">
                    @lang('Klusuma greznība Siguldas sirdī!')
                </p>
            </div>
            <div class="gap-6 lg:gap-18 lg:grid lg:grid-cols-2">
                <img class="rounded-3xl h-full object-cover w-full" src="{{ asset('images/siguldas-skati-home-5.jpg') }}"
                     alt="@lang('Siguldas Skati - Moduļu māju parks')"/>
                <div class="lg:flex lg:flex-col lg:justify-center">
                    <h3 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                        @lang('Siguldas skati')
                    </h3>
                    <div class="space-y-6 text-justify text-base ">
                        <p>
                            {{-- prettier-ignore --}}
                            @lang('Īpaša atpūtas vieta tiem, kuri meklē mieru, klātbūtnes un skaistuma sajūtu pašā
                            Siguldas sirdī. Mūsu stāsts sākas vietā, kur dizains saplūst ar dabu un miers kļūst par
                            lielāko greznību.')
                        </p>
                        <p>
                            {{-- prettier-ignore --}}
                            @lang('Atrodoties tieši līdzās Panorāmas ratam un Svētku laukumam, esam radījuši modernas
                            brīvdienu dizaina mājas, kas piedāvā ne tikai naktsmītni, bet arī sajūtu pieredzi. Šeit
                            ainava kļūst par interjera daļu, un katrs gadalaiks sniedz jaunu skatpunktu – no miglainiem
                            rudens rītiem līdz sniegotām virsotnēm vai saulainām vasaras dienām.')
                        </p>
                        <p>
                            {{-- prettier-ignore --}}
                            @lang('Mēs piedāvājam vietu, kur vienkāršība nozīmē kvalitāti, minimālisms – apzinātu
                            komfortu, un katrā detaļā jūtama mīlestība pret vietu, kur dzīvojam. Šis projekts ir mūsu
                            aicinājums atgriezties pie tā, kas būtisks – miera, klātbūtnes un skaistuma.')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCT CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 pb-12 md:pb-18 lg:pb-24 xl:pb-30">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Dizaina mājas, sauna un džakūzis')
                </h2>

            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    {{-- FIRST SECONDARY BANNER --}}
    <x-banner>
        <x-slot name="bannerBackgroundImage">
            {{ asset('images/siguldas-skati-home-3.jpg') }}
        </x-slot>
        <x-slot name="bannerBackgroundImageAlt">
            @lang('Drona skats uz māju parku')
        </x-slot>
    </x-banner>

    {{-- GALLERY --}}
    {{-- <div class="bg-ss"> --}}
    {{-- <div class="container mx-auto px-4 pt-12 md:pt-18 lg:pt-24 xl:pt-30"> --}}
    {{-- <div class="relative mb-3 inline-block"> --}}
    {{-- <h2 class="text-h-mob lg:text-h-md leading-none border-b-2"> --}}
    {{-- @lang('Galerija') --}}
    {{-- </h2> --}}

    {{-- </div> --}}
    {{-- <p class="text-ss-gray pb-6 text-sm leading-none xl:pb-12"> --}}
    {{-- @lang('Siguldas skatu galerija.') --}}
    {{-- </p> --}}
    {{-- <x-carousels.gallery.wrapper></x-carousels.gallery.wrapper> --}}
    {{-- </div> --}}
    {{-- </div> --}}

    {{-- EXPERIENCES --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Ko sniedz pieredze Siguldas Skatos?')
                </h2>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none sm:pb-12">
                @lang('Dizaina brīvdienu mājas ar skatu uz Siguldu!')
            </p>
            <div class="mb-6 grid grid-cols-1 gap-6 text-white sm:grid-cols-2 xl:grid-cols-4">
                <x-experience-card>
                    <x-slot name="experienceSvg">wave</x-slot>
                    <x-slot name="experienceTitle">
                        {{-- prettier-ignore --}}
                        @lang('Klusums un miers')
                    </x-slot>
                    <x-slot name="experienceText">
                        {{-- prettier-ignore --}}
                        @lang('Mūsu brīvdienu dizaina mājās nav steigas – šī ir vieta, kur Tu vari elpot dziļāk, dzirdēt
                        sevi un atpūsties bez stresa.')
                    </x-slot>
                </x-experience-card>
                <x-experience-card>
                    <x-slot name="experienceSvg">check</x-slot>
                    <x-slot name="experienceTitle">
                        {{-- prettier-ignore --}}
                        @lang('Estētika un komforts')
                    </x-slot>
                    <x-slot name="experienceText">
                        {{-- prettier-ignore --}}
                        @lang('Pārdomāts dizains, kvalitatīvas detaļas un mājīgums, kas ļauj justies kā mājās – tikai
                        vēl labāk.')
                    </x-slot>
                </x-experience-card>
                <x-experience-card>
                    <x-slot name="experienceSvg">happy_face</x-slot>
                    <x-slot name="experienceTitle">
                        {{-- prettier-ignore --}}
                        @lang('Atmiņas un sajūtas')
                    </x-slot>
                    <x-slot name="experienceText">
                        {{-- prettier-ignore --}}
                        @lang('Šī nav tikai naktsmītne – tā ir iespēja apstāties, sajust vidi un ieraudzīt Siguldu citām
                        acīm.')
                    </x-slot>
                </x-experience-card>
                <x-experience-card>
                    <x-slot name="experienceSvg">location</x-slot>
                    <x-slot name="experienceTitle">
                        {{-- prettier-ignore --}}
                        @lang('Izcila lokācija')
                    </x-slot>
                    <x-slot name="experienceText">
                        {{-- prettier-ignore --}}
                        @lang('Vietu pašā Siguldas sirdī, kur daba un pilsētas kultūras notikumi satiekas viena soļa
                        attālumā.')
                    </x-slot>
                </x-experience-card>
            </div>
        </div>
    </div>

    {{-- SECOND SECONDARY BANNER --}}
    <x-banner>
        <x-slot name="bannerImage">
            {{ asset('images/siguldas-skati-logo.svg') }}
        </x-slot>
        <x-slot name="bannerBackgroundImage">
            {{ asset('images/siguldas-skati-home-4.jpg') }}
        </x-slot>
        <x-slot name="bannerText">
            {{-- prettier-ignore --}}
            @lang('Sigulda ir vairāk nekā galamērķis – tā ir sajūta.')
        </x-slot>

        <x-slot name="bannerBackgroundImageAlt">@lang('Sigulda Skati Sauna')</x-slot>
    </x-banner>

    {{-- WHAT TO DO IN SIGULDA --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 pt-12 md:pt-18 lg:pt-24 xl:pt-30">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Ko vēl darīt Siguldā?')
                </h2>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none md:max-w-2/3 lg:max-w-2/5 xl:pb-12">
                {{-- prettier-ignore --}}
                @lang('Sigulda ir vieta, kur daba, kultūra un piedzīvojumi saplūst vienā ainavā. Neatkarīgi no
                gadalaika, šeit katrs var atrast sev piemērotu ritmu – vai tā būtu nesteidzīga pastaiga dabas takās,
                kultūras baudījums vai mazs piedzīvojums virs koku galotnēm.')
            </p>
            <x-carousels.todo.wrapper></x-carousels.todo.wrapper>
        </div>
    </div>

    {{-- WHAT TO DO IN SIGULDA MODALS --}}
    <x-main-modal>
        <x-slot name="modalId">nature-trails</x-slot>
        <x-slot name="modalHeading">@lang('Siguldas dabas takas – piedzīvojums jebkurā gadalaikā')</x-slot>
        <x-slot name="modalContent">
            {{-- prettier-ignore --}}
            <p class="mb-2">@lang('Sigulda ir viena no Latvijas ainaviskākajām vietām, kur daba atklājas visā krāšņumā. Pilsētu ieskauj Gaujas Nacionālā parka takas, kas piemērotas gan mierīgām pastaigām, gan aktīvai atpūtai. Te iespējams izbaudīt elpu aizraujošus skatus no Gaujas senlejas kraujām, šķērsot vēsturiskos tiltiņus, iepazīt alas un avotus, kā arī doties maršrutos, kas ved cauri mežiem, pļavām un gar upes krastiem.')</p>
            <p>@lang('Populārākās takas ir Paradīzes taka, Kājnieku tilta maršruts, Krimuldas dabas taka un Velnalas taka – katra no tām piedāvā unikālu pieredzi un savas ainavas. Pavasarī un vasarā tās ir pilnas ar zaļumu un ziediem, rudenī – krāsainu lapu jūru, bet ziemā pārtop klusā, sniegotā pasakā.')</p>
        </x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">active-recreation</x-slot>
        <x-slot name="modalHeading">@lang('Siguldas aktīvā atpūta – piedzīvojumi ikvienam')</x-slot>
        <x-slot name="modalContent">
            {{-- prettier-ignore --}}
            <p class="mb-2">@lang('Sigulda ir Latvijas aktīvās atpūtas galvaspilsēta, kur piedzīvojumi pieejami visa gada garumā. Vasarā pilsēta piedāvā iespēju izmēģināt trošu un rodeļu nobraucienus piedzīvojuma parkā “Tarzāns” gaisa tramvaju pāri Gaujai, braucienus ar SUP dēļiem, kā arī velomaršrutus dažādās grūtības pakāpēs. Īpašu adrenalīna devu nodrošina bobsleja un kamaniņu trase, kur iespējams izjust ātrumu gan vasarā, gan ziemā.')</p>
            <p class="mb-2">@lang('Rudenī un pavasarī Sigulda piesaista pārgājienu entuziastus ar krāšņām Gaujas Nacionālā parka takām, bet ziemā pilsēta pārtop par slēpošanas un snovborda centru ar vairākām trasēm un aprīkojuma nomu.')</p>
            <p>@lang('Neatkarīgi no sezonas Siguldā iespējams apvienot aktīvu atpūtu ar kultūras un dabas baudījumu, radot neaizmirstamu pieredzi ikvienam viesim.')</p>
        </x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">taste-experience</x-slot>
        <x-slot name="modalHeading">@lang('Siguldas kafejnīcas un restorāni – garšu ceļojums pilsētas sirdī')</x-slot>
        <x-slot name="modalContent">
            {{-- prettier-ignore --}}
            <p class="mb-2">@lang('Sigulda piedāvā daudzveidīgu gastronomisko pieredzi – no mājīgām kafejnīcām līdz izsmalcinātiem restorāniem. Pilsētas centrā atrodami gan bistro ar sezonāliem ēdieniem, gan vietas, kas specializējas tradicionālajā latviešu virtuvē. Gardēžus priecēs arī desertu kafejnīcas, kurās iespējams nogaršot vietējo konditoru darinājumus.')</p>
            <p class="mb-2">@lang('Vakara noskaņām piemēroti ir restorāni ar plašu vīnu un kokteiļu izvēli. Savukārt mierīgai pēcpusdienai ideāli iederēsies nelielas kafejnīcas, kur baudīt svaigi grauzdētu kafiju un mājās gatavotus desertus.')</p>
            <p class="mb2">@lang('Siguldas gastronomiskā ainava apvieno vietējo raksturu ar starptautiskām garšu tendencēm, piedāvājot izvēles iespējas ikvienai gaumei un noskaņojumam – gan ātrām maltītēm pēc aktīvās atpūtas, gan nesteidzīgām vakariņām īpašā atmosfērā.')</p>
            <p>@lang('Iesakām apmeklēt: ESI cafe (Pils 4b), Aparjods (Ventas iela 1A), Lielais Loms suši (Krišjāņa Valdemāra iela 2).')</p>
        </x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">culture-and-history</x-slot>
        <x-slot name="modalHeading">@lang('Siguldas vēsture un kultūras dzīve')</x-slot>
        <x-slot name="modalContent">
            {{-- prettier-ignore --}}
            <p class="mb-2">@lang('Sigulda, bieži dēvēta par “Vidzemes Šveici”, ir pilsēta ar bagātu vēsturi un izteiktu kultūras identitāti. Pirmās rakstītās ziņas par Siguldu datētas ar 13. gadsimtu, kad tika uzcelts Siguldas viduslaiku pils komplekss, vēlāk papildināts ar Turaidas pili un Krimuldas muižu. Šie vēsturiskie objekti šodien ir nozīmīgas kultūras un tūrisma vietas, kas stāsta par reģiona attīstību vairāk nekā astoņu gadsimtu garumā.')</p>
            <p class="mb-2">@lang('Sigulda vienmēr bijusi kultūras un mākslas centrs, kur regulāri notiek mūzikas festivāli, teātra izrādes, tautas tradīciju pasākumi un mākslas izstādes. Īpaši izceļas Siguldas Opermūzikas svētki un vasaras koncerti pilsdrupās, kas apvieno unikālu vidi ar augstvērtīgu muzikālo programmu.')</p>
            <p>@lang('Mūsdienās pilsēta harmoniski apvieno senatnes mantojumu ar mūsdienīgu kultūras dzīvi, piedāvājot gan vietējiem iedzīvotājiem, gan viesiem daudzveidīgas iespējas izzināt vēsturi, baudīt mākslu un piedalīties radošos notikumos. No viduslaiku pilsdrupām līdz laikmetīgajām mākslas instalācijām – Sigulda ir vieta, kur vēsture un kultūra dzīvo līdzās un papildina viena otru.')</p>
        </x-slot>
    </x-main-modal>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById("header-carousel");
        const options = {
            Autoplay: {
                timeout: 5000,
                showProgress: false
            },
            Navigation: false,
            Dots: false
        };
        new Carousel(container, options);
    })
</script>