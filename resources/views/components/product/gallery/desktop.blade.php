<div class="hidden lg:grid grid-cols-2 2xl:grid-cols-5 gap-4 mb-6">
    <div class="2xl:col-span-3 overflow-hidden rounded-3xl h-full">
        <a data-fancybox="galleryDesktop" href="{{ asset('images/siguldas-skati-product-3.jpg') }}"
            class="block h-full">
            <img class="rounded-3xl object-cover w-full h-148 transition-transform duration-300 ease-in-out hover:scale-105"
                alt="" src="{{ asset('images/siguldas-skati-product-3.jpg') }}">
        </a>
    </div>
    <div class="2xl:col-span-2 grid grid-cols-2 grid-rows-2 gap-4 h-full">
        <div class="overflow-hidden rounded-3xl h-full">
            <a data-fancybox="galleryDesktop" href="{{ asset('images/siguldas-skati-product-viz-1.jpg') }}"
                class="block h-full">
                <img class="rounded-3xl object-cover w-full h-72 transition-transform duration-300 ease-in-out hover:scale-105"
                    alt="" src="{{ asset('images/siguldas-skati-product-viz-1.jpg') }}">
            </a>
        </div>
        <div class="overflow-hidden rounded-3xl h-full">
            <a data-fancybox="galleryDesktop" href="{{ asset('images/gallery/siguldas-skati-house-1-2.jpg') }}"
                class="block h-full">
                <img class="rounded-3xl object-cover w-full h-72 transition-transform duration-300 ease-in-out hover:scale-105"
                    alt="" src="{{ asset('images/gallery/siguldas-skati-house-1-2.jpg') }}">
            </a>
        </div>
        <div class="overflow-hidden rounded-3xl h-full">
            <a data-fancybox="galleryDesktop" href="{{ asset('images/gallery/siguldas-skati-house-1-1.jpg') }}"
                class="block h-full">
                <img class="rounded-3xl object-cover w-full h-72 transition-transform duration-300 ease-in-out hover:scale-105"
                    alt="" src="{{ asset('images/gallery/siguldas-skati-house-1-1.jpg') }}">
            </a>
        </div>
        <div class="overflow-hidden rounded-3xl h-full">
            <a data-fancybox="galleryDesktop" href="{{ asset('images/siguldas-skati-product-1.jpg') }}"
                class="block h-full">
                <img class="rounded-3xl object-cover w-full h-72 transition-transform duration-300 ease-in-out hover:scale-105"
                    alt="" src="{{ asset('images/siguldas-skati-product-1.jpg') }}">
            </a>
        </div>
    </div>
</div>

<script type="module">
    Fancybox.bind('[data-fancybox="galleryDesktop"]', {
    compact: false,
    idle: false,
    
    animated: false,
    showClass: false,
    hideClass: false,
    
    dragToClose: false,
    contentClick: false,
    
    Images: {
    
    },
    
    Toolbar: {
    display: {
    left: [],
    middle: ['infobar'],
    right: ['close'],
    },
    },
    
    Thumbs: {
    type: 'classic',
    },
    });

</script>