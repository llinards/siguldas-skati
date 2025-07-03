<figure class="f-carousel__slide relative group transition-all duration-300 ease-in-out rounded-3xl">
    <a id="gallery-main-{{ $imageGallery }}" href="{{ $imageLink ?? '' }}" @if(!empty($extraImages))
        data-gallery-extra='{{ $extraImages }}' @endif>
        <div class="relative rounded-3xl overflow-hidden">
            <img class="rounded-3xl object-cover w-full h-108 md:h-116 xl:h-132 transition-all duration-300 ease-in-out group-hover:scale-105 group-hover:rounded-2xl"
                alt="" src="{{ $imageLink ?? '' }}" />
        </div>
    </a>
</figure>