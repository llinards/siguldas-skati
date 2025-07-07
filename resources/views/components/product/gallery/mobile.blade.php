<div class="block lg:hidden grid grid-cols-1 relative md:mb-6">
    @if(!$product->images->isEmpty())
        <x-carousels.nav class="lg:hidden">
            <x-slot name="prev">productGalleryPrev</x-slot>
            <x-slot name="next">productGalleryNext</x-slot>
        </x-carousels.nav>
        <div id="productGallery" class="f-carousel">
            <div class="f-carousel__viewport">
                @foreach($product->images as $image)
                    <figure class="f-carousel__slide">
                        <div class="overflow-hidden rounded-3xl w-full">
                            <a data-fancybox="galleryMob" href="{{ Storage::url($image->filename) }}"
                               class="">
                                <img
                                    class="rounded-3xl object-cover duration-300 transition-all ease-in-out h-108 w-full"
                                    alt="" src="{{ Storage::url($image->filename) }}">
                            </a>
                        </div>
                    </figure>
                @endforeach
            </div>
            <div id="carousel-counter"
                 class="absolute right-4 bottom-4 bg-black/60 text-white text-xs px-2 py-1 rounded z-10">
                1 / 5
            </div>
        </div>
    @endif
</div>
<script type="module">
    const carousel = new Carousel(document.getElementById('productGallery'), {
        Navigation: false,
        infinite: false,
        center: true,
        transition: 'fade',
        Dots: false,
    });

    document.getElementById('productGalleryPrev').addEventListener('click', () => carousel.slidePrev());
    document.getElementById('productGalleryNext').addEventListener('click', () => carousel.slideNext());

    const prevBtn = document.getElementById('productGalleryPrev');
    const nextBtn = document.getElementById('productGalleryNext');

    function updateProductGalleryNav() {
        prevBtn.disabled = carousel.page === 0;
        nextBtn.disabled = carousel.page === carousel.pages.length - 1;
    }

    function slideCounter() {
        const counter = document.getElementById('carousel-counter');
        const current = carousel.page + 1;
        const total = carousel.pages.length;
        counter.innerText = `${current} / ${total}`;
    }

    function handleResize() {
        updateProductGalleryNav();
        slideCounter();
    }

    window.addEventListener('resize', handleResize);

    carousel.on('ready', () => {
        updateProductGalleryNav();
        slideCounter();
    });

    carousel.on('change', () => {
        updateProductGalleryNav();
        slideCounter();
    });

    carousel.off('resize');

    Fancybox.bind('[data-fancybox="galleryMob"]', {
        compact: false,
        idle: false,
        animated: false,
        showClass: false,
        hideClass: false,
        dragToClose: false,
        contentClick: false,
        Images: {},
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
