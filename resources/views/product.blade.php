<x-app-layout>
    <div class="bg-ss">
        <div class="container mx-auto px-4 grid grid-cols-1">
            <x-product.title>
                <x-slot name="productTitle">@lang('Brīvdienu dizaina māja') Orebro</x-slot>
                <x-slot name="productCapacityLong">@lang('Paredzēts 2 personām')</x-slot>
            </x-product.title>

            <x-product.gallery.mobile/>
            <div class="flex sm:hidden mt-6 mb-6">
                <x-btn-primary class="w-full">@lang('Rezervēt')</x-btn-primary>
            </div>

            <x-product.gallery.desktop/>

            <x-product.wrapper/>

        </div>
    </div>

    {{-- PRODUCT CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative inline-block mb-3">
                <h2 class="text-h-mob lg:text-h-md leading-none">@lang('Apskati citas dizaina mājas!')</h2>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray pb-6 leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')</p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>
</x-app-layout>
