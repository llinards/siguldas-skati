<figure class="f-carousel__slide relative">
    <img class="rounded-3xl object-cover w-full" alt="{{ $productTitle ?? '' }}"
        data-lazy-src="{{ $productImage ?? '' }}" />
    <div class="absolute inset-0 rounded-3xl pointer-events-none"
        style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));"></div>
    <div class="absolute w-2/5 left-1/2 bottom-12 -translate-x-1/2 z-10 text-white text-center">
        <h4 class="mb-2 text-h-sm-mob sm:text-5xl md:text-4xl xl:text-5xl">{{ $productTitle ?? '' }}
        </h4>
        <p class="mb-6 text-base sm:text-lg xl:text-xl 2xl:text-2xl">{{ $productCapacity ?? '' }}</p>
        <x-btn-secondary href="{{ $productLink ?? '' }}">
            @lang('Rezervēt')
        </x-btn-secondary>
    </div>
</figure>