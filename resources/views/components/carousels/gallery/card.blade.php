<figure class="f-carousel__slide relative group border-2 border-transparent hover:border-ss-dark transition-all duration-300 ease-in-out rounded-3xl">
    <a id="gallery-main-{{ $imageGallery }}" href="{{ $imageLink ?? '' }}" @if(!empty($extraImages))
        data-gallery-extra='{{ $extraImages }}' @endif>
        <div class="relative rounded-3xl overflow-hidden">
            <img class="rounded-3xl object-cover w-full min-h-136 transition-all duration-300 ease-in-out group-hover:scale-105 group-hover:rounded-2xl"
                alt="" src="{{ $imageLink ?? '' }}" />
        </div>
    </a>
</figure>