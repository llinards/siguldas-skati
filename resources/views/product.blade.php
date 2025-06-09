<x-app-layout>
    <div class="bg-ss">
        <div class="container mx-auto px-4 grid grid-cols-1">
            <x-product.title>
                <x-slot name="productTitle">@lang('Brīvdienu dizaina māja') Orebro</x-slot>
                <x-slot name="productCapacityLong">@lang('Paredzēts 2 personām')</x-slot>
            </x-product.title>

            <div>
                <div class="block lg:hidden grid grid-cols-1 relative">
                    <x-carousels.nav class="lg:hidden">
                        <x-slot name="prev">productPrev</x-slot>
                        <x-slot name="next">productNext</x-slot>
                    </x-carousels.nav>
                    <div id="productCarousel" class="f-carousel">
                        <div class="f-carousel__viewport">
                            <x-product.gallery-mobile />
                        </div>
                        <div id="carousel-counter"
                            class="absolute right-4 bottom-4 bg-black/60 text-white text-xs px-2 py-1 rounded z-10">
                            1 / 5
                        </div>
                    </div>
                </div>

                <x-product.gallery-desktop />
            </div>

        </div>
    </div>
</x-app-layout>