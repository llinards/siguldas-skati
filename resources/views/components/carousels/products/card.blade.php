<figure class="f-carousel__slide group relative rounded-3xl transition-all duration-300 ease-in-out">
    <a href="{{ $productLink ?? '' }}">
        <div class="relative overflow-hidden rounded-3xl">
            <img
                class="h-108 w-full rounded-3xl object-cover transition-transform duration-300 ease-in-out group-hover:scale-105 md:h-116 xl:h-132"
                alt="{{ $productTitle ?? '' }}"
                src="{{ $productImage ?? '' }}"
            />
            <div
                class="pointer-events-none absolute inset-0 rounded-3xl bg-gradient-to-t from-black/30 via-black/20 to-transparent transition-transform duration-300 ease-in-out group-hover:scale-105"
            ></div>
        </div>

        <div
            class="absolute bottom-12 left-1/2 z-10 flex w-full max-w-[320px] -translate-x-1/2 flex-col items-center text-center text-white"
        >
            <h4 class="text-h-sm-mob mb-2 sm:text-5xl md:text-4xl xl:text-5xl">
                {{ $productTitle ?? '' }}
            </h4>
            <p class="mb-3 text-base sm:text-lg xl:text-xl 2xl:text-2xl">
                {{ $productCapacity ?? '' }}
            </p>
            <x-btn-secondary href="{{ $productLink ?? '' }}">
                @lang('Uzzināt vairāk')
            </x-btn-secondary>
        </div>
    </a>
</figure>
