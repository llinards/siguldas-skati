<x-app-layout :title="$product->title" :description="$product->description">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <x-product.title>
                <x-slot name="productTitle">{{ $product->title }}</x-slot>
                <x-slot name="productCapacityLong">
                    @lang('Paredzēts')
                    {{
                    $product->person_count === 1
                    ? __('1 personai')
                    : __(':count personām', ['count' => $product->person_count])
                    }}
                </x-slot>
            </x-product.title>

            <x-product.gallery.mobile :product="$product"/>
            <div class="mt-6 mb-6 sm:hidden">
                <x-btn-primary
                    href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html">@lang('Rezervēt')</x-btn-primary>
            </div>

            <x-product.gallery.desktop :product="$product"/>

            <x-product.wrapper :product="$product"/>
        </div>
    </div>

    {{-- PRODUCTS CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">'
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Apskati citas dizaina mājas!')
                </h2>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    <x-main-modal>
        <x-slot name="modalId">product-modal</x-slot>
        <x-slot name="modalHeading">@lang('Papildērtības')</x-slot>
        <x-slot name="modalContent">
            <ul class="sm:grid mb-6 grid-cols-2 grid-rows-5 space-y-3 mt-4">
                @foreach ($product->features as $feature)
                    <li class="flex items-center gap-x-4">
                        <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}"
                             class="h-8 w-8"/>
                        {{ $feature->title }}
                    </li>
                @endforeach
            </ul>
        </x-slot>
        <x-slot name="modalCTA">@lang('Rezervēt')</x-slot>
    </x-main-modal>
</x-app-layout>
