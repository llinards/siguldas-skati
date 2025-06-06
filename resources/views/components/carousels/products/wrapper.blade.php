<div class="bg-ss">
    <div class="container mx-auto carousel-section grid grid-cols-1">
        <x-carousels.nav>
            <x-slot name="prev">productPrev</x-slot>
            <x-slot name="next">productNext</x-slot>
        </x-carousels.nav>
        <div id="productCarousel" class="f-carousel">
            <div class="f-carousel__viewport">
                <x-carousels.products.card>
                    <x-slot name="productTitle">Boden</x-slot>
                    <x-slot name="productImage">{{ asset('images/siguldas-skati-product-1.jpg') }}</x-slot>
                    <x-slot name="productCapacity">@lang('2
                        personām')</x-slot>
                    <x-slot name="productLink">#</x-slot>
                </x-carousels.products.card>
                <x-carousels.products.card>
                    <x-slot name="productTitle">Boden</x-slot>
                    <x-slot name="productImage">{{ asset('images/siguldas-skati-product-2.jpg') }}</x-slot>
                    <x-slot name="productCapacity">@lang('4
                        personām')</x-slot>
                    <x-slot name="productLink">#</x-slot>
                </x-carousels.products.card>
                <x-carousels.products.card>
                    <x-slot name="productTitle">Boden</x-slot>
                    <x-slot name="productImage">{{ asset('images/siguldas-skati-product-3.jpg') }}</x-slot>
                    <x-slot name="productCapacity">@lang('4
                        personām')</x-slot>
                    <x-slot name="productLink">#</x-slot>
                </x-carousels.products.card>
                <x-carousels.products.card>
                    <x-slot name="productTitle">Boden</x-slot>
                    <x-slot name="productImage">{{ asset('images/siguldas-skati-product-4.jpg') }}</x-slot>
                    <x-slot name="productCapacity">@lang('2
                        personām')</x-slot>
                    <x-slot name="productLink">#</x-slot>
                </x-carousels.products.card>
            </div>
        </div>
    </div>
</div>

<script type="module">
    const carousel = new Carousel(document.getElementById('productCarousel'), {
    Navigation: false,
    infinite: false,
    center: true,
    transition: 'fade',
    Dots: false,
    });
    
    document.getElementById('productPrev').addEventListener('click', () => carousel.slidePrev());
    document.getElementById('productNext').addEventListener('click', () => carousel.slideNext());
    
    const prevBtn = document.getElementById('productPrev');
    const nextBtn = document.getElementById('productNext');
    
    function updateCarouselNav() {
        prevBtn.disabled = carousel.page === 0;
        nextBtn.disabled = carousel.page === carousel.pages.length - 1;
    }

    updateCarouselNav();

    carousel.on('change', updateCarouselNav);
</script>