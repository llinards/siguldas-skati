<figure class="f-carousel__slide group relative rounded-3xl transition-all duration-300 ease-in-out">
    <a
        id="gallery-main-{{ $title }}"
        href="{{ $cover ?? "" }}"
        @if (! empty($images))
            data-gallery-extra='{{ $images }}'
        @endif
    >
        <div class="relative overflow-hidden rounded-3xl">
            <img
                class="h-108 w-full rounded-3xl object-cover transition-all duration-300 ease-in-out group-hover:scale-105 group-hover:rounded-2xl md:h-116 xl:h-132"
                alt=""
                src="{{ $cover ?? "" }}"
            />
        </div>
    </a>
</figure>
