<figure class="f-carousel__slide">
    <div class="overflow-hidden rounded-3xl w-full">
        <a data-fancybox="galleryMob" href="{{ asset('images/siguldas-skati-product-3.jpg') }}" class="">
            <img class="rounded-3xl object-cover duration-300 transition-all ease-in-out h-107 w-full" alt=""
                src="{{ asset('images/siguldas-skati-product-3.jpg') }}">
        </a>
    </div>
</figure>
<figure class="f-carousel__slide">
    <div class="overflow-hidden rounded-3xl w-full">
        <a data-fancybox="galleryMob" href="{{ asset('images/siguldas-skati-product-viz-1.jpg') }}" class="">
            <img class="rounded-3xl object-cover transition-all ease-in-out h-107 w-full" alt=""
                src="{{ asset('images/siguldas-skati-product-viz-1.jpg') }}">
        </a>
    </div>
</figure>
<figure class="f-carousel__slide">
    <div class="overflow-hidden rounded-3xl w-full">
        <a data-fancybox="galleryMob" href="{{ asset('images/gallery/siguldas-skati-house-1-2.jpg') }}" class="">
            <img class="rounded-3xl object-cover transition-all ease-in-out h-107 w-full" alt=""
                src="{{ asset('images/gallery/siguldas-skati-house-1-2.jpg') }}">
        </a>
    </div>
</figure>
<figure class="f-carousel__slide">
    <div class="overflow-hidden rounded-3xl w-full">
        <a data-fancybox="galleryMob" href="{{ asset('images/gallery/siguldas-skati-house-1-1.jpg') }}" class="">
            <img class="rounded-3xl object-cover transition-all ease-in-out h-107 w-full" alt=""
                src="{{ asset('images/gallery/siguldas-skati-house-1-1.jpg') }}">
        </a>
    </div>
</figure>
<figure class="f-carousel__slide">
    <div class="overflow-hidden rounded-3xl w-full">
        <a data-fancybox="galleryMob" href="{{ asset('images/siguldas-skati-product-1.jpg') }}" class="">
            <img class="rounded-3xl object-cover transition-all ease-in-out h-107 w-full" alt=""
                src="{{ asset('images/siguldas-skati-product-1.jpg') }}">
        </a>
    </div>
</figure>

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

    function updateProductCarouselNav() {
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
        updateProductCarouselNav();
        slideCounter();
    }

    window.addEventListener('resize', handleResize);

    carousel.on('ready', () => {
        updateProductCarouselNav();
        slideCounter();
    });

    carousel.on('change', () => {
        updateProductCarouselNav();
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