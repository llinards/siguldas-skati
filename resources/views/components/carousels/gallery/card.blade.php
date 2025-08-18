<figure class="f-carousel__slide group relative rounded-3xl transition-all duration-300 ease-in-out cursor-pointer">
    <a
        id="gallery-main-{{ $title }}"
        href="{{ $cover ?? "" }}"
        @if (! empty($images))
            data-gallery-extra='{{ $images }}'
        @endif
        data-fancybox="gallery"
    >
        <div class="relative overflow-hidden rounded-3xl">
            <img
                class="h-108 w-full rounded-3xl object-cover transition-all duration-300 ease-in-out group-hover:scale-105 group-hover:rounded-2xl md:h-116 xl:h-132"
                alt=""
                src="{{ $cover ?? "" }}"
            />
            <div
                class="pointer-events-none absolute inset-0 rounded-3xl bg-black/50 transition-transform duration-300 ease-in-out group-hover:scale-105">
            </div>
        </div>
    </a>

    <div
        class="absolute bottom-12 left-1/2 z-10 flex w-full max-w-[320px] -translate-x-1/2 flex-col items-center text-center text-white">
        <!-- Siguldas Skati Logo -->
        <div class="mb-10">
            <img
                src=" {{ asset('images/siguldas-skati-logo.svg') }}"
                alt="Siguldas Skati"
                class="h-25 w-auto opacity-90"
            />
        </div>

        <h4 class="text-h-sm-mob mb-3 sm:text-2xl md:text-3xl xl:text-4xl">
            {{ $title ?? '' }}
        </h4>
        <button
            type="button"
            onclick="this.closest('figure').querySelector('a').click();"
            class="px-6 py-4 uppercase hover:bg-ss-dark rounded-xl hover:text-white
                                                              transition-all duration-200
                                                              text-center bg-white border-black text-black cursor-pointer"
        >
            @lang('Skatīt')
        </button>
    </div>

</figure>
