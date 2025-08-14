<x-app-layout :title="__('Dizaina mājas, sauna un džakūzis')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <x-btn-back class="pb-3"/>
                <h1 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Dizaina mājas, sauna un džakuzi')
                </h1>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Rezervē savu brīvdienu māju jau tagad!')
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
                                {{ $product->person_count === 1 ? __('1 pieaugušais') : __(':count pieaugušie', ['count' =>
                      $product->person_count]) }}
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
            @endif
        </div>
    </div>
</x-app-layout>
