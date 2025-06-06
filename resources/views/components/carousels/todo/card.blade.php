<figure class="f-carousel__slide relative">
    <img class="rounded-3xl object-cover w-full" alt="{{ $todoTitle ?? '' }}" data-lazy-src="{{ $todoImage ?? '' }}" />
    <div class="absolute inset-0 rounded-3xl pointer-events-none"
        style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));"></div>
    <div class="absolute w-4/6 left-1/2 bottom-12 -translate-x-1/2 z-10 text-white text-center">
        <h4 class="mb-6 text-h-sm-mob sm:text-2xl md:text-3xl xl:text-4xl">{{ $todoTitle ?? '' }}
        </h4>
        <x-btn-secondary href="{{ $todoLink ?? '' }}" class="">
            @lang('Uzzināt vairāk')
        </x-btn-secondary>
    </div>
</figure>