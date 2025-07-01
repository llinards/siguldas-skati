<div>
    <x-admin.flash-message/>

    <!-- Products Grid -->
    <div wire:sortable="updateProductOrder" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                 wire:key="product-{{ $product->id }}"
                 wire:sortable.item="{{ $product->id }}">
                <!-- Product Image -->
                <div class="aspect-square bg-gray-200 relative">
                    @if($product->cover)
                        <img src="{{ asset($product->cover) }}"
                             alt="{{ $product->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? __('Aktīvs') : __('Neaktīvs') }}
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate" title="{{ $product->title }}">
                        {{ $product->title }}
                    </h3>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2">
                        <!-- Toggle Active Status -->
                        <button
                            wire:click="toggleActive({{ $product->id }})"
                            class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200
                                {{ $product->is_active
                                    ? 'bg-red-100 text-red-800 hover:bg-red-200'
                                    : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            {{ $product->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <div class="flex gap-2">
                            <!-- Edit Button -->
                            <a href="{{route('product.edit', $product->id)}}" wire:navigate
                               class="inline-flex items-center p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <!-- Delete Button -->
                            <button
                                wire:click="deleteProduct({{ $product->id }})"
                                wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo produktu?') }}"
                                class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-base leading-7.5 md:text-xl xl:text-2xl">@lang('Šobrīd nav aktīvu māju!')</p>
            </div>
        @endforelse
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end">
        <x-btn-primary>{{ __('Pievienot produktu') }}</x-btn-primary>
    </div>

</div>
