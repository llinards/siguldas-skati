<div class="relative block grid grid-cols-1 md:mb-6 lg:hidden">
    @if (! $product->images->isEmpty())
    <x-carousels.nav class="lg:hidden">
        <x-slot name="prev">productGalleryPrev</x-slot>
        <x-slot name="next">productGalleryNext</x-slot>
    </x-carousels.nav>
    <div id="productGallery" class="f-carousel">
        <div class="f-carousel__viewport">
            @foreach ($product->images as $image)
            <figure class="f-carousel__slide slide_count">
                <div class="w-full overflow-hidden rounded-3xl">
                    <a data-fancybox="galleryMob" href="{{ Storage::url($image->filename) }}" class="">
                        <img class="h-108 w-full rounded-3xl object-cover transition-all duration-300 ease-in-out"
                            alt="" src="{{ Storage::url($image->filename) }}" />
                    </a>
                </div>
            </figure>
            @endforeach
        </div>
        <div id="carousel-counter"
            class="absolute right-4 bottom-4 z-10 rounded bg-black/60 px-2 py-1 text-xs text-white">
            1 / 5
        </div>
    </div>
    @endif
</div>
<script type="module">
    const allSlides = document.querySelectorAll('.slide_count')
    function whichSlideIsSelected(slides) {
        const selected = [...slides].filter(slide => slide.classList.contains('is-selected'));
        return selected[selected.length - 1] || null;
    };
    

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
        const selectedSlide = whichSlideIsSelected(allSlides);
        const current = parseInt(selectedSlide.dataset.index) + 1;
        const total = allSlides.length;
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