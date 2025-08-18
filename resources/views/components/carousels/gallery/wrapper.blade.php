<div class="bg-ss">
    <div class="carousel-section container mx-auto grid grid-cols-1">
        <x-carousels.nav>
            <x-slot name="prev">galleryPrev</x-slot>
            <x-slot name="next">galleryNext</x-slot>
        </x-carousels.nav>
        <div id="galleryCarousel" class="f-carousel">
            <div class="f-carousel__viewport">
                @foreach($galleries as $gallery)
                    <x-carousels.gallery.card>
                        <x-slot name="title">{{$gallery->title}}</x-slot>
                        <x-slot name="cover">
                            {{ Storage::url($gallery->images->first()->filename) }}
                        </x-slot>
                        <x-slot name="images">
                            {{ json_encode($gallery->images->skip(1)->pluck('filename')->map(fn($filename) => Storage::url($filename))->toArray()) }}
                        </x-slot>
                    </x-carousels.gallery.card>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script type="module">
    const carousel = new Carousel(document.getElementById('galleryCarousel'), {
        Navigation: false,
        infinite: false,
        center: true,
        transition: 'fade',
        Dots: false,
        Autoplay: false,
    });

    document.getElementById('galleryPrev').addEventListener('click', () => carousel.slidePrev());
    document.getElementById('galleryNext').addEventListener('click', () => carousel.slideNext());

    const prevBtn = document.getElementById('galleryPrev');
    const nextBtn = document.getElementById('galleryNext');

    function updateGalleryCarouselNav() {
        prevBtn.disabled = carousel.page === 0;
        nextBtn.disabled = carousel.page === carousel.pages.length - 1;
    }

    updateGalleryCarouselNav();

    carousel.on('change', updateGalleryCarouselNav);

    Fancybox.bind('[data-fancybox="gallery"]', {
        compact: false,
        idle: false,

        animated: false,
        showClass: false,
        hideClass: false,

        dragToClose: false,
        contentClick: false,

        Images: {
            zoom: false,
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
