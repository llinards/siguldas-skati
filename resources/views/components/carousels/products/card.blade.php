<figure class="f-carousel__slide relative">
    <a href="{{ $productLink ?? '' }}">
        <div class="group relative rounded-3xl overflow-hidden">
            <img class="rounded-3xl object-cover w-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                alt="{{ $productTitle ?? '' }}" src="{{ $productImage ?? '' }}" />
            <div
                class="absolute inset-0 rounded-3xl pointer-events-none bg-gradient-to-t from-black/30 via-black/20 to-transparent transition-transform duration-300 ease-in-out group-hover:scale-105">
            </div>
        </div>
    </a>
    <div
        class="absolute w-full max-w-[320px] left-1/2 bottom-12 -translate-x-1/2 z-10 text-white text-center flex flex-col items-center">
        <h4 class="mb-2 text-h-sm-mob sm:text-5xl md:text-4xl xl:text-5xl">{{ $productTitle ?? '' }}</h4>
        <p class="mb-3 text-base sm:text-lg xl:text-xl 2xl:text-2xl">{{ $productCapacity ?? '' }}</p>
        <x-btn-secondary href="{{ $productLink ?? '' }}">
            @lang('Rezervēt')
        </x-btn-secondary>
    </div>
</figure>