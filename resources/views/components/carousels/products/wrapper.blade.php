<div class="bg-ss">
    <div class="container mx-auto grid grid-cols-1">
        @if ($products->isEmpty())
            <p class="text-base leading-7.5 md:text-xl xl:text-2xl">
                @lang('Šobrīd nav aktīvu māju!')
            </p>
        @else
            <x-carousels.nav>
                <x-slot name="prev">productPrev</x-slot>
                <x-slot name="next">productNext</x-slot>
            </x-carousels.nav>
            <div id="productCarousel" class="f-carousel">
                <div class="f-carousel__viewport">
                    @foreach ($products as $product)
                        <x-carousels.products.card>
                            <x-slot name="productTitle">
                                {{ $product->title }}
                            </x-slot>
                            <x-slot name="productImage">
                                {{ Storage::url($product->cover) }}
                            </x-slot>
                            <x-slot name="productCapacity">
                                {{ $product->person_count === 1 ? __('1 personai') : __(':count personām', ['count' => $product->person_count]) }}
                                @if($product->children_count > 0)
                                    +  {{ $product->children_count === 1 ? __('1 bērns') : __(':count bērni', ['count' =>
                                $product->children_count]) }}
                                @endif
                            </x-slot>
                            <x-slot name="productLink">
                                {{ route('product', $product->slug) }}
                            </x-slot>
                        </x-carousels.products.card>
                    @endforeach
                </div>
            </div>
        @endif
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
