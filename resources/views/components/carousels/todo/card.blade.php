<figure class="f-carousel__slide relative">
    <a href="{{ $todoLink ?? '' }}">
        <div class="group relative rounded-3xl overflow-hidden">
            <img class="rounded-3xl object-cover w-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                alt="{{ $todoTitle ?? '' }}" data-lazy-src="{{ $todoImage ?? '' }}" />
            <div class="absolute inset-0 rounded-3xl pointer-events-none bg-gradient-to-t from-black/30 via-black/20 to-transparent transition-transform duration-300 ease-in-out group-hover:scale-105"></div>
        </div>
    </a>
    <div class="absolute w-full max-w-[320px] left-1/2 bottom-12 -translate-x-1/2 z-10 text-white text-center flex flex-col items-center">
        <h4 class="mb-3 text-h-sm-mob sm:text-2xl md:text-3xl xl:text-4xl">{{ $todoTitle ?? '' }}</h4>
        <x-btn-secondary href="{{ $todoLink ?? '' }}">
            @lang('Uzzināt vairāk')
        </x-btn-secondary>
    </div>
</figure>