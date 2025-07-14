<div>
    <x-admin.flash-message/>
    <form wire:submit="save">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Rediģēt ērtības') }} - {{ $product->getTranslation('title', app()->getLocale()) }}
                </h2>

                @if (!$features->isEmpty())
                    <div class="mt-8">
                        <div class="grid grid-cols-1 gap-4 grid-cols-2 sm:grid-cols-4">
                            @foreach ($features as $feature)
                                <label class="relative flex justify-center items-center cursor-pointer rounded-lg border p-4 hover:bg-gray-50">
                                    <div class="relative mr-2">
                                        <input
                                                type="checkbox"
                                                value="{{ $feature->id }}"
                                                wire:model="selectedFeatures"
                                                class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"
                                        />
                                        <svg
                                                class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                                fill="none"
                                                viewBox="0 0 20 20"
                                                stroke="currentColor"
                                                stroke-width="3"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-center">
                                        @if ($feature->icon_image)
                                            <img src="{{ Storage::url($feature->icon_image) }}"
                                                 alt="{{ $feature->getTranslation('title', app()->getLocale()) }}"
                                                 class="h-10 w-10 object-contain mr-2"
                                            />
                                        @else
                                            <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-gray-500 text-xs">{{ __('Nav') }}</span>
                                            </div>
                                        @endif
                                        <span class="block text-sm font-medium text-gray-900">{{ $feature->title }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    {{ __('Nav pievienotas ērtības. Lūdzu, vispirms pievieno ērtības') }}
                                    <a href="{{ route('dashboard.feature.add') }}"
                                       class="font-medium underline text-yellow-700 hover:text-yellow-600">
                                        {{ __('šeit.') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('dashboard') }}" class="text-sm/6 font-semibold text-gray-900">
                @lang('Atpakaļ')
            </a>
            @if (!$features->isEmpty())
                <x-btn-primary type="submit" class="">
                    @lang('Saglabāt')
                </x-btn-primary>
            @endif
        </div>
    </form>
</div>
