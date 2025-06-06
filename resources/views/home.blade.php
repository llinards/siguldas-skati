<x-app-layout :title="__('Sākums')">

    {{-- HEADER --}}
    <div class="px-2 relative bg-cover bg-center bg-no-repeat flex justify-center home-introduction ">
        <div class="container mx-auto flex flex-col items-center px-4">
            <h1
                class="text-h-mob xs:text-6xl sm:text-7xl md:text-8xl xl:text-h max-w-7xl font-heading uppercase text-center text-white z-10 absolute top-48 sm:top-1/2 sm:-translate-y-1/2 leading-12 sm:leading-16 md:leading-24 xl:leading-28">
                @lang('Modernas
                brīvdienu
                dizaina
                mājas tavai
                atpūtai!')</h1>

            <x-btn-header href="#" class="absolute bottom-48 sm:bottom-16 z-10">
                @lang('Uzzināt vairāk')
            </x-btn-header>
        </div>
    </div>

    {{-- ABOUT US --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
            <div>
                <div class="relative inline-block mb-3">
                    <h2 class="text-h-mob lg:text-h-md leading-none">@lang('Par mums')</h2>
                    <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
                </div>
                <p class="text-sm text-ss-gray mb-6 leading-none">
                    @lang('Klusuma greznība Siguldas sirdī!')
                </p>
            </div>
            <div class="lg:grid lg:grid-cols-2 gap-6">
                <img class="lg:h-240 object-cover rounded-3xl" src="{{ asset('images/siguldas-skati-home-2.jpg') }}"
                    alt="@lang('Siguldas Skati - Moduļu māju parks')">
                <div class="lg:flex lg:flex-col lg:justify-center">
                    <h3 class="mt-6 lg:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-sm">@lang('Siguldas skati')</h3>
                    <div
                        class="text-base leading-7.5 md:text-xl xl:text-2xl space-y-6 md:space-y-10 md:leading-10 text-justify">
                        <p>@lang('Īpaša atpūtas vieta tiem, kuri meklē mieru, klātbūtnes un skaistuma sajūtu pašā
                            Siguldas
                            sirdī.
                            Mūsu
                            stāsts sākas
                            vietā, kur dizains saplūst ar dabu un miers kļūst par lielāko greznību.')</p>
                        <p>
                            @lang('Atrodoties tieši līdzās Panorāmas ratam un Svētku laukumam, esam radījuši modernas
                            brīvdienu
                            dizaina
                            mājas, kas piedāvā
                            ne tikai naktsmītni, bet arī sajūtu pieredzi. Šeit ainava kļūst par interjera daļu, un katrs
                            gadalaiks
                            sniedz jaunu
                            skatpunktu – no miglainiem rudens rītiem līdz sniegotām virsotnēm vai saulainām vasaras
                            dienām.')
                        </p>
                        <p> @lang('Mēs piedāvājam vietu, kur vienkāršība nozīmē kvalitāti, minimālisms – apzinātu
                            komfortu,
                            un
                            katrā
                            detaļā
                            jūtama
                            mīlestība pret vietu, kur dzīvojam. Šis projekts ir mūsu aicinājums atgriezties pie tā, kas
                            būtisks
                            –
                            miera, klātbūtnes
                            un skaistuma.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCT CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4 pb-12 md:pb-18 lg:pb-24 xl:pb-30">
            <div class="relative inline-block mb-3">
                <h2 class="text-h-mob lg:text-h-md leading-none">@lang('Dizaina mājas un sauna')</h2>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray mb-6 leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')</p>
            <x-carousels.products.wrapper></x-carousels.products.wrapper>
        </div>
    </div>

    {{-- FIRST SECONDARY BANNER --}}
    <x-banner>
        <x-slot name="bannerImage">{{ asset('images/siguldas-skati-home-3.jpg') }}</x-slot>
        <x-slot name="bannerText">@lang('Miers nav kaut kur tālu!
            tas ir šeit - starp dizainu, dabu un Tevi!')</x-slot>
        <x-slot name="bannerImageAlt">@lang('Drona skats uz māju parku')</x-slot>
    </x-banner>

    {{-- EXPERIENCES --}}
    <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
        <div class="relative inline-block mb-3">
            <h2 class="text-h-mob lg:text-h-md leading-none">@lang('Ko sniedz pieredze Siguldas Skatos?')</h2>
            <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
        </div>
        <p class="text-sm text-ss-gray mb-6 leading-none">
            @lang('Dizaina brīvdienu mājas ar skatu uz Siguldu!')</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6 text-white">
            <x-experience-card>
                <x-slot name="experienceSvg">wave</x-slot>
                <x-slot name="experienceTitle">@lang('Klusums
                    un miers')</x-slot>
                <x-slot name="experienceText">@lang('Mūsu brīvdienu dizaina mājās nav steigas – šī ir vieta, kur Tu vari
                    elpot dziļāk, dzirdēt sevi un atpūsties bez stresa.')</x-slot>
            </x-experience-card>
            <x-experience-card>
                <x-slot name="experienceSvg">check</x-slot>
                <x-slot name="experienceTitle">@lang('Estētika un
                    komforts')</x-slot>
                <x-slot name="experienceText">@lang('Pārdomāts dizains, kvalitatīvas detaļas un mājīgums, kas ļauj
                    justies
                    kā mājās – tikai vēl labāk.')</x-slot>
            </x-experience-card>
            <x-experience-card>
                <x-slot name="experienceSvg">happy_face</x-slot>
                <x-slot name="experienceTitle">@lang('Atmiņas un
                    sajūtas')</x-slot>
                <x-slot name="experienceText">@lang('Šī nav tikai naktsmītne – tā ir iespēja apstāties, sajust vidi un
                    ieraudzīt Siguldu citām acīm.')</x-slot>
            </x-experience-card>
            <x-experience-card>
                <x-slot name="experienceSvg">location</x-slot>
                <x-slot name="experienceTitle">@lang('Izcila
                    lokācija')</x-slot>
                <x-slot name="experienceText">@lang('Vietu pašā Siguldas sirdī, kur daba un pilsētas kultūras notikumi
                    satiekas viena soļa attālumā.')</x-slot>
            </x-experience-card>
        </div>
    </div>

    {{-- SECOND SECONDARY BANNER --}}
    <x-banner>
        <x-slot name="bannerImage">{{ asset('images/siguldas-skati-home-4.jpg') }}</x-slot>
        <x-slot name="bannerText">@lang('Sigulda nav tikai galamērķis –
            tā ir sajūta.')</x-slot>
        <x-slot name="secondaryBannerText">@lang('Mēs esam tepat, lai palīdzētu Tev to iepazīt savā ritmā.')
        </x-slot>
        <x-slot name="bannerImageAlt">@lang('Sigulda Skati Sauna')</x-slot>
    </x-banner>


</x-app-layout>