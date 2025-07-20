<x-app-layout :title="$product->title" :description="$product->description">
    <div class="bg-ss">
        <div class="container mx-auto grid grid-cols-1 px-4">
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

            <x-product.gallery.mobile :product="$product" />
            <div class="mt-6 mb-6 sm:hidden">
                <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
            </div>

            <x-product.gallery.desktop :product="$product" />

            <x-product.wrapper :product="$product" />
        </div>
    </div>

    {{-- PRODUCTS CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Apskati citas dizaina mājas!')
                </h2>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    <x-modal name="example-modal">
        <div class="flex items-center justify-between border-ss-dark border-b px-4 py-3">
            <h3 class="text-h-sm-mob lg:text-h-mob leading-none mt-2">@lang('Papildērtības')</h3>
            </h3>
            <span id="modalBtnClose" type="button" x-on:click="$dispatch('close-modal', 'example-modal')"
                class="bg-ss-dark hover:bg-white inline-flex size-8 items-center justify-center gap-x-2 rounded-full border border-transparent hover:border-ss-dark transition-all duration-200 cursor-pointer group"
                aria-label="Close" tabindex="2">
                <span class="sr-only">Close</span>
                <svg class="size-4 shrink-0 stroke-white group-hover:stroke-ss-dark group-hover:duration-200"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </span>
        </div>
        <div class="p-4">
            <ul class="sm:grid mb-6 grid-cols-2 grid-rows-5 space-y-3 mt-4">
                @foreach ($product->features as $feature)
                <li class="flex items-center gap-x-4">
                    <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}" class="h-8 w-8" />
                    {{ $feature->title }}
                </li>
                @endforeach
            </ul>
        </div>
    </x-modal>

</x-app-layout>