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
    @if ($experiences->isNotEmpty())
        <div class="bg-ss">
            <div class="container mx-auto px-4 py-12 md:py-18 lg:py-24 xl:py-30">
                <div class="relative mb-3 inline-block">
                    <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                        {{ $experiencesTitle }}
                    </h2>
                </div>
                <p class="text-ss-gray pb-6 text-sm leading-none sm:pb-12">
                    {{ $experiencesSubtitle }}
                </p>
                <div class="mb-6 grid grid-cols-1 gap-6 text-white sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($experiences as $experience)
                        <x-experience-card
                            :iconUrl="$experience->icon_image ? Storage::url($experience->icon_image) : ''"
                            :experienceTitle="$experience->title"
                        >
                            <x-slot name="experienceText">{!! $experience->description !!}</x-slot>
                        </x-experience-card>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- WHAT TO DO IN SIGULDA --}}
    @if ($activities->isNotEmpty())
        <div class="bg-ss">
            <div class="container mx-auto px-4">
                <div class="relative mb-3 inline-block">
                    <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                        {{ $activitiesTitle }}
                    </h2>
                </div>
                <p class="text-ss-gray pb-6 text-sm md:max-w-2/3 lg:max-w-2/5 xl:pb-12">
                    {{ $activitiesSubtitle }}
                </p>
                <x-carousels.todo.wrapper :activities="$activities"></x-carousels.todo.wrapper>
            </div>
        </div>

        {{-- WHAT TO DO IN SIGULDA MODALS --}}
        @foreach ($activities as $activity)
            <x-main-modal>
                <x-slot name="modalId">activity-{{ $activity->id }}</x-slot>
                <x-slot name="modalHeading">{{ $activity->modal_heading }}</x-slot>
                <x-slot name="modalContent">{!! $activity->modal_content !!}</x-slot>
            </x-main-modal>
        @endforeach
    @endif
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
