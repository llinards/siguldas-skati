<x-app-layout :title="__('Sākums')">

    <button type="button"
        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-vertically-centered-modal"
        data-hs-overlay="#hs-vertically-centered-modal">
        Vertically centered modal
    </button>

    <div id="hs-vertically-centered-modal"
        class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
        role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-modal-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div
                class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                <div
                    class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="hs-vertically-centered-modal-label" class="font-bold text-gray-800 dark:text-white">
                        Modal title
                    </h3>
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600"
                        aria-label="Close" data-hs-overlay="#hs-vertically-centered-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto">
                    <p class="text-gray-800 dark:text-neutral-400">
                        This is a wider card with supporting text below as a natural lead-in to additional content.
                    </p>
                </div>
                <div
                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                    <button type="button"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                        data-hs-overlay="#hs-vertically-centered-modal">
                        Close
                    </button>
                    <button type="button"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="home-introduction relative flex justify-center bg-cover bg-center bg-no-repeat px-2">
        <div class="container mx-auto flex flex-col items-center px-4">
            <h1
                class="text-h-mob xs:text-6xl xl:text-h font-heading absolute top-48 z-10 max-w-7xl text-center leading-12 text-white uppercase sm:top-1/2 sm:-translate-y-1/2 sm:text-7xl sm:leading-16 md:text-8xl md:leading-24 xl:leading-28">
                {{-- prettier-ignore --}}
                @lang('Modernas brīvdienu dizaina mājas tavai atpūtai!')
            </h1>

            <x-btn-header href="#about-us" class="absolute bottom-48 z-10 sm:bottom-16">
                @lang('Uzzināt vairāk')
            </x-btn-header>
        </div>
    </div>

    {{-- ABOUT US --}}
    <div id="about-us" class="bg-ss">
        <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
            <div>
                <div class="relative mb-3 inline-block">
                    <h2 class="text-h-mob lg:text-h-md leading-none">
                        @lang('Par mums')
                    </h2>
                    <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
                </div>
                <p class="text-ss-gray pb-6 text-sm leading-none">
                    @lang('Klusuma greznība Siguldas sirdī!')
                </p>
            </div>
            <div class="gap-6 lg:grid lg:grid-cols-2">
                <img class="rounded-3xl object-cover lg:h-240" src="{{ asset('images/siguldas-skati-home-2.jpg') }}"
                    alt="@lang('Siguldas Skati - Moduļu māju parks')" />
                <div class="lg:flex lg:flex-col lg:justify-center">
                    <h3 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                        @lang('Siguldas skati')
                    </h3>
                    <div
                        class="space-y-6 text-justify text-base leading-7.5 md:space-y-10 md:text-xl md:leading-10 xl:text-2xl">
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
                <h2 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Dizaina mājas un sauna')
                </h2>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    {{-- FIRST SECONDARY BANNER --}}
    <x-banner>
        <x-slot name="bannerImage">
            {{ asset('images/siguldas-skati-home-3.jpg') }}
        </x-slot>
        <x-slot name="bannerText">
            {{-- prettier-ignore --}}
            @lang('Miers nav kaut kur tālu! Tas ir šeit - starp dizainu, dabu un Tevi!')
        </x-slot>
        <x-slot name="bannerImageAlt">
            @lang('Drona skats uz māju parku')
        </x-slot>
    </x-banner>

    {{-- GALLERY --}}
    {{-- <div class="bg-ss"> --}}
        {{-- <div class="container mx-auto px-4 pt-12 md:pt-18 lg:pt-24 xl:pt-30"> --}}
            {{-- <div class="relative mb-3 inline-block"> --}}
                {{-- <h2 class="text-h-mob lg:text-h-md leading-none"> --}}
                    {{-- @lang('Galerija') --}}
                    {{-- </h2> --}}
                {{-- <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span> --}}
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
                <h2 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Ko sniedz pieredze Siguldas Skatos?')
                </h2>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
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
            {{ asset('images/siguldas-skati-home-4.jpg') }}
        </x-slot>
        <x-slot name="bannerText">
            {{-- prettier-ignore --}}
            @lang('Sigulda nav tikai galamērķis – tā ir sajūta.')
        </x-slot>
        <x-slot name="secondaryBannerText">
            {{-- prettier-ignore --}}
            @lang('Mēs esam tepat, lai palīdzētu Tev to iepazīt savā ritmā.')
        </x-slot>
        <x-slot name="bannerImageAlt">@lang('Sigulda Skati Sauna')</x-slot>
    </x-banner>

    {{-- WHAT TO DO IN SIGULDA --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 pt-12 md:pt-18 lg:pt-24 xl:pt-30">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Ko vēl darīt Siguldā?')
                </h2>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
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
</x-app-layout>