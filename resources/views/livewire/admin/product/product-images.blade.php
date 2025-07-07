<div>
    <x-admin.flash-message/>
    <form wire:submit="store">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="mb-3 leading-none text-h-sm-mob lg:text-h-mob">{{ __('Rediģēt galeriju') }}
                    - {{$product->title}}</h2>

                <!-- Existing Images -->
                @if($product->images->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Esošie attēli') }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                             wire:sortable="updateImageOrder">
                            @foreach($product->images as $image)
                                <div class="relative group" wire:key="existing-image-{{ $image->id }}"
                                     wire:sortable.item="{{ $image->id }}">
                                    <img src="{{ Storage::url($image->filename) }}"
                                         alt="{{ __('Galerijas attēls') }}"
                                         class="h-32 w-full rounded-md border border-gray-300 object-cover">
                                    <button type="button"
                                            wire:click="removeImage({{ $image->id }})"
                                            wire:confirm="Vai tiešām vēlaties dzēst šo attēlu?"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Images Preview -->
                @if($images && count($images) > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Jaunie attēli') }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($images as $index => $image)
                                <div class="relative group">
                                    <div class="relative">
                                        <img src="{{ $image->temporaryUrl() }}"
                                             alt="{{ __('Priekšskatījums') }}"
                                             class="h-32 w-full rounded-md object-cover {{ $this->isImageOversized($index) ? 'border-2 border-red-500' : 'border border-gray-300' }}">

                                        @if($this->isImageOversized($index))
                                            <div
                                                class="absolute top-0 left-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-t-md">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                {{ $this->getImageSizeInKB($index) }} KB
                                            </div>
                                        @else
                                            <div
                                                class="absolute top-0 left-0 bg-green-500 text-white text-xs px-2 py-1 rounded-tl-md">
                                                {{ $this->getImageSizeInKB($index) }} KB
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button"
                                            wire:click="removeNewImage({{ $index }})"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload Area -->
                <div class="mt-5">
                    <label class="block text-sm/6 font-medium text-gray-900 mb-2">{{ __('Pievienot attēlus') }}</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="gallery-upload"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
                               wire:loading.class="pointer-events-none cursor-not-allowed bg-gray-100 opacity-75"
                               wire:loading.class.remove="cursor-pointer bg-gray-50 hover:bg-gray-100"
                               wire:target="images">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <!-- Loading indicator -->
                                <div wire:loading wire:target="images" class="flex flex-col items-center">
                                    <svg class="animate-spin w-8 h-8 mb-2 text-gray-600" fill="none"
                                         viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 font-medium">{{ __('Augšupielādē...') }}</p>
                                </div>

                                <!-- Default state -->
                                <div wire:loading.remove wire:target="images">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">{{ __('Noklikšķiniet, lai augšupielādētu') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ __('PNG, JPG vai JPEG (Maks. 512 KB katram)') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Varat izvēlēties vairākus attēlus') }}</p>
                                </div>
                            </div>
                            <input id="gallery-upload"
                                   type="file"
                                   wire:model="images"
                                   class="hidden"
                                   accept="image/*"
                                   multiple
                                   wire:loading.attr="disabled"
                                   wire:target="images">
                        </label>
                    </div>
                    <!-- Show both array-level errors and individual file errors -->
                    <x-input-error :messages="$errors->get('images')" class="mt-2"/>
                    <x-input-error :messages="collect($errors->get('images.*'))->flatten()" class="mt-2"/>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{route('product.edit', $product)}}"
               class="text-sm/6 font-semibold text-gray-900">@lang('Atpakaļ')</a>
            <x-btn-primary type="submit" class="">@lang('Saglabāt')</x-btn-primary>
        </div>
    </form>
</div>
