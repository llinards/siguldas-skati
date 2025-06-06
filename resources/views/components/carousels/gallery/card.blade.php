<figure class="f-carousel__slide relative">
    <a id="gallery-main-{{ $imageGallery }}" href="{{ $imageLink ?? '' }}" @if(!empty($extraImages))
        data-gallery-extra='{{ $extraImages }}' @endif>
        <img class="rounded-3xl object-cover w-full min-h-136 transition-transform duration-300 ease-in-out hover:scale-105" alt="" src="{{ $imageLink ?? '' }}" />
    </a>
</figure>