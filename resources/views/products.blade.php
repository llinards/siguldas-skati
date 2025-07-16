<x-app-layout :title="__('Dizaina mājas un sauna')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-36 mb-3 inline-block">
                <h1 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Dizaina mājas un sauna')
                </h1>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izvēlies...')
            </p>

            @if ($products->isEmpty())
                <p class="text-base leading-7.5 md:text-xl xl:text-2xl">
                    @lang('Šobrīd nav aktīvu māju!')
                </p>
            @else
                <div class="md:hidden">
                    <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
                </div>

                <div class="hidden gap-4 md:grid md:grid-cols-2 lg:grid-cols-3">
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
                            </x-slot>
                            <x-slot name="productLink">
                                {{ route('product', $product->slug) }}
                            </x-slot>
                        </x-carousels.products.card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
