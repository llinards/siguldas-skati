<x-app-layout :title="__('Sākums')">
    {{-- HEADER --}}
    <div class="home-introduction relative flex justify-center bg-black overflow-hidden">
        @if ($headerMedia->isNotEmpty())
            @php($singleSlide = $headerMedia->count() === 1)
            <!-- Carousel background -->
            <div class="absolute inset-0 z-0">
                <div class="f-carousel" id="header-carousel">
                    @foreach ($headerMedia as $item)
                        <div class="f-carousel__slide relative w-full">
                            <div class="absolute inset-0 bg-black/50 z-10"></div>
                            @if ($item->isVideo())
                                <video src="{{ Storage::url($item->filename) }}" class="w-full h-screen object-cover"
                                    data-header-video muted autoplay playsinline preload="metadata"
                                    @if ($singleSlide) loop @endif aria-label="@lang('Siguldas Skati')"></video>
                            @else
                                <img src="{{ Storage::url($item->filename) }}" class="w-full h-screen object-cover"
                                    alt="@lang('Siguldas Skati')">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Foreground content -->
        <div class="container relative z-10 mx-auto flex flex-col justify-end items-center px-4">
            <h1
                class="mb-6 text-h-mob xs:text-6xl font-heading max-w-7xl text-center leading-12 text-white uppercase xs:leading-16 md:text-6xl">
                {{ $heroTitle }}
            </h1>

            <x-btn-header target="_blank" href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html"
                class="mb-10 md:mb-34">
                @lang('Rezervēt')
            </x-btn-header>
        </div>
    </div>

    {{-- ABOUT US --}}
    @if (isset($aboutTitle))
        <div id="about-us" class="bg-ss">
            <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
                <div>
                    <div class="relative mb-3 inline-block">
                        <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                            {{ $aboutTitle }}
                        </h2>
                    </div>
                    <p class="text-ss-gray pb-6 text-sm leading-none">
                        {{ $aboutSubtitle }}
                    </p>
                </div>
                <div class="gap-6 lg:gap-18 lg:grid lg:grid-cols-2">
                    <img class="rounded-3xl h-full object-cover w-full"
                        src="{{ $aboutImage ? Storage::url($aboutImage) : asset('images/siguldas-skati-home-5.jpg') }}"
                        alt="@lang('Siguldas Skati - Moduļu māju parks')" />
                    <div class="lg:flex lg:flex-col lg:justify-center">
                        <h3 class="text-h-sm-mob lg:text-h-sm mt-6 mb-3 leading-none lg:mt-0">
                            {{ $aboutHeading }}
                        </h3>
                        <div class="about-us-description space-y-6 text-justify text-base">
                            {!! $aboutDescription !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PRODUCT CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    {{ $productsTitle }}
                </h2>

            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                {{ $productsSubtitle }}
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    {{-- GALLERY --}}
    @if ($galleries->isNotEmpty())
        <div id="galerija" class="bg-ss">
            <div class="container mx-auto px-4 pt-12 md:pt-18 lg:pt-24 xl:pt-30">
                <div class="relative mb-3 inline-block">
                    <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                        {{ $galleryTitle }}
                    </h2>

                </div>
                <p class="text-ss-gray pb-6 text-sm leading-none xl:pb-12">
                    {{ $gallerySubtitle }}
                </p>
                <x-carousels.gallery.wrapper :galleries="$galleries"></x-carousels.gallery.wrapper>
            </div>
        </div>
    @endif

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
                        @lang('Mūsu brīvdienu dizaina mājās nav steigas – šī ir vieta, kur Tu vari elpot dziļāk, dzirdēt sevi un atpūsties bez stresa.')
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
                        @lang('Pārdomāts dizains, kvalitatīvas detaļas un mājīgums, kas ļauj justies kā mājās – tikai vēl labāk.')
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
                        @lang('Šī nav tikai naktsmītne – tā ir iespēja apstāties, sajust vidi un ieraudzīt Siguldu citām acīm.')
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
                        @lang('Vietu pašā Siguldas sirdī, kur daba un pilsētas kultūras notikumi satiekas viena soļa attālumā.')
                    </x-slot>
                </x-experience-card>
            </div>
        </div>
    </div>

    {{-- WHAT TO DO IN SIGULDA --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Ko vēl darīt Siguldā?')
                </h2>
            </div>
            <p class="text-ss-gray pb-6 text-sm md:max-w-2/3 lg:max-w-2/5 xl:pb-12">
                {{-- prettier-ignore --}}
                @lang('Sigulda ir vieta, kur daba, kultūra un piedzīvojumi saplūst vienā ainavā. Neatkarīgi no gadalaika, šeit katrs var atrast sev piemērotu ritmu – vai tā būtu nesteidzīga pastaiga dabas takās, kultūras baudījums vai mazs piedzīvojums virs koku galotnēm.')
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
        if (!container) {
            return;
        }

        const carousel = new Carousel(container, {
            Autoplay: {
                timeout: 5000,
                showProgress: false
            },
            Navigation: false,
            Dots: false
        });

        let pendingTimeout = null;
        let pendingEndedHandler = null;
        let pendingVideo = null;

        function clearPending() {
            if (pendingTimeout) {
                clearTimeout(pendingTimeout);
                pendingTimeout = null;
            }
            if (pendingVideo && pendingEndedHandler) {
                pendingVideo.removeEventListener("ended", pendingEndedHandler);
            }
            pendingVideo = null;
            pendingEndedHandler = null;
        }

        function handleActiveSlide() {
            clearPending();

            const slides = container.querySelectorAll(".f-carousel__slide");
            const activeIndex = typeof carousel.page === "number" ? carousel.page : 0;
            const activeSlide = slides[activeIndex];
            const video = activeSlide && activeSlide.querySelector("video[data-header-video]");

            if (!video) {
                return;
            }

            if (carousel.Autoplay && typeof carousel.Autoplay.pause === "function") {
                carousel.Autoplay.pause();
            }

            try {
                video.currentTime = 0;
            } catch (_) {}

            const playResult = video.play();
            if (playResult && typeof playResult.catch === "function") {
                playResult.catch(function() {});
            }

            pendingVideo = video;
            pendingEndedHandler = function() {
                clearPending();
                if (typeof carousel.slideNext === "function") {
                    carousel.slideNext();
                }
                if (carousel.Autoplay && typeof carousel.Autoplay.resume === "function") {
                    carousel.Autoplay.resume();
                }
            };
            video.addEventListener("ended", pendingEndedHandler);

            pendingTimeout = setTimeout(function() {
                clearPending();
                if (typeof carousel.slideNext === "function") {
                    carousel.slideNext();
                }
                if (carousel.Autoplay && typeof carousel.Autoplay.resume === "function") {
                    carousel.Autoplay.resume();
                }
            }, 30000);
        }

        carousel.on("change", handleActiveSlide);
        handleActiveSlide();
    })
</script>
