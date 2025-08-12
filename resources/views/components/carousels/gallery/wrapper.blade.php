<div class="bg-ss">
    <div class="carousel-section container mx-auto grid grid-cols-1">
        <x-carousels.nav class="xl:hidden">
            <x-slot name="prev">galleryPrev</x-slot>
            <x-slot name="next">galleryNext</x-slot>
        </x-carousels.nav>
        <div id="galleryCarousel" class="f-carousel">
            <div class="f-carousel__viewport">
                <x-carousels.gallery.card>
                    <x-slot name="imageGallery">galleryOne</x-slot>
                    <x-slot name="imageLink">
                        {{ asset('images/gallery/siguldas-skati-house-1-1.jpg') }}
                    </x-slot>
                    <x-slot name="extraImages">
                        {{ json_encode([asset('images/gallery/siguldas-skati-house-1-2.jpg')]) }}
                    </x-slot>
                </x-carousels.gallery.card>
                <x-carousels.gallery.card>
                    <x-slot name="imageGallery">galleryTwo</x-slot>
                    <x-slot name="imageLink">
                        {{ asset('images/gallery/siguldas-skati-house-2-1.jpg') }}
                    </x-slot>
                    <x-slot name="extraImages">
                        {{ json_encode([asset('images/gallery/siguldas-skati-house-2-2.jpg')]) }}
                    </x-slot>
                </x-carousels.gallery.card>
                <x-carousels.gallery.card>
                    <x-slot name="imageGallery">galleryThree</x-slot>
                    <x-slot name="imageLink">
                        {{ asset('images/gallery/siguldas-skati-house-3-1.jpg') }}
                    </x-slot>
                    <x-slot name="extraImages">
                        {{ json_encode([asset('images/gallery/siguldas-skati-house-3-2.jpg')]) }}
                    </x-slot>
                </x-carousels.gallery.card>
                <x-carousels.gallery.card>
                    <x-slot name="imageGallery">galleryFour</x-slot>
                    <x-slot name="imageLink">
                        {{ asset('images/gallery/siguldas-skati-house-4-1.jpg') }}
                    </x-slot>
                    <x-slot name="extraImages">
                        {{ json_encode([asset('images/gallery/siguldas-skati-house-4-2.jpg')]) }}
                    </x-slot>
                </x-carousels.gallery.card>
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