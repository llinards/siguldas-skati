<x-app-layout :title="__('Sākums')">
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

    {{-- WHAT TO DO IN SIGULDA MODALS --}}
    <x-main-modal>
        <x-slot name="modalId">nature-trails</x-slot>
        <x-slot name="modalHeading">@lang('Dabas takas')</x-slot>
        <x-slot name="modalContent">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum, quasi rem explicabo corrupti
                necessitatibus aperiam illo quam placeat consequatur laudantium beatae sequi quae dolorem? Voluptate,
                impedit laboriosam. Illo, commodi rem!
                Consequuntur, tempora ad alias perspiciatis a, neque tempore, voluptatem dignissimos quasi quos in
                quibusdam est! Rem expedita eum qui nobis beatae aut quas facilis illo, totam, voluptate maxime aliquid.
                Eveniet.
                Fuga veniam temporibus, blanditiis totam obcaecati deleniti laborum, unde ratione doloremque et
                aspernatur ut, quibusdam earum nam iste facilis reprehenderit! Quo repellat expedita fuga numquam, ipsa
                harum adipisci voluptatum officia.</p>
        </x-slot>
        <x-slot name="modalCTA">@lang('Uzzināt vairāk')</x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">active-recreation</x-slot>
        <x-slot name="modalHeading">@lang('Aktīva atpūta')</x-slot>
        <x-slot name="modalContent">
            <p><span>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quisquam nisi et beatae consequatur vero
                    qui nobis quam libero quis aperiam fugiat sunt, magni provident excepturi dicta quidem neque. Velit,
                    repellendus!</span><span>Reiciendis odit, qui temporibus nulla placeat, incidunt dicta ea harum
                    quidem cupiditate ullam ducimus tempore ipsa corporis aut optio saepe quis amet corrupti. Laborum,
                    nisi id asperiores culpa commodi dignissimos.</span></p>
        </x-slot>
        <x-slot name="modalCTA">@lang('Uzzināt vairāk')</x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">taste-experience</x-slot>
        <x-slot name="modalHeading">@lang('Garšu pieredze')</x-slot>
        <x-slot name="modalContent">
            <p><span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam similique expedita quas omnis
                    vitae architecto quam aperiam, voluptate hic. Totam corrupti maiores ipsam pariatur. Similique
                    tenetur nobis quas eaque vero!</span></p>
        </x-slot>
        <x-slot name="modalCTA">@lang('Uzzināt vairāk')</x-slot>
    </x-main-modal>
    <x-main-modal>
        <x-slot name="modalId">culture-and-history</x-slot>
        <x-slot name="modalHeading">@lang('Kultūra un vēsture')</x-slot>
        <x-slot name="modalContent">
            <p>
                <span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore incidunt reiciendis architecto
                    quidem optio corporis laudantium numquam excepturi sunt, nulla assumenda provident accusamus
                    suscipit hic dolore explicabo itaque molestias porro!</span>
                <span>Repellendus suscipit nobis tempora, beatae distinctio possimus numquam quibusdam cupiditate modi
                    veritatis unde natus similique inventore id corporis in perferendis laboriosam, ratione fugit
                    deleniti. Dolorum error fugit distinctio ad consequuntur.</span>
                <span>Temporibus laudantium ut quis quo laborum dicta necessitatibus non eveniet pariatur dolorum,
                    consequuntur iste alias odit, id laboriosam rem. Officia iure molestias vitae quaerat ullam
                    blanditiis vero obcaecati cum voluptatum!</span>
                <span>Ratione facere quae neque itaque commodi nobis suscipit amet nulla dicta quas, voluptatibus
                    deleniti laborum laboriosam enim officiis. Inventore nemo fugiat hic eum enim asperiores saepe atque
                    optio dignissimos odio.</span>
            </p>
        </x-slot>
        <x-slot name="modalCTA">@lang('Uzzināt vairāk')</x-slot>
    </x-main-modal>
</x-app-layout>