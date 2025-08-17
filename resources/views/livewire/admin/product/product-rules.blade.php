<div>
    <x-admin.flash-message/>
    <form wire:submit="save">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Rediģēt noteikumus') }} - {{ $product->getTranslation('title', app()->getLocale()) }}
                </h2>

                @if (! ($houseRules->isEmpty() && $safetyRules->isEmpty()))
                    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <h3 class="mb-3 font-semibold">{{ __('Mājokļa noteikumi') }}</h3>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($houseRules as $rule)
                                    <label
                                        class="relative flex cursor-pointer items-center justify-start rounded-lg border p-4 hover:bg-gray-50">
                                        <div class="relative mr-3">
                                            <input type="checkbox" value="{{ $rule->id }}" wire:model="selectedRules"
                                                   class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"/>
                                            <svg
                                                class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                                fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                                            </svg>
                                        </div>
                                        @if ($rule->icon_image)
                                            <img src="{{ Storage::url($rule->icon_image) }}"
                                                 alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                                 class="mr-2 h-8 w-8 object-contain"/>
                                        @endif
                                        <span class="block text-sm font-medium text-gray-900">{{ $rule->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-3 font-semibold">{{ __('Drošība un īpašums') }}</h3>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($safetyRules as $rule)
                                    <label
                                        class="relative flex cursor-pointer items-center justify-start rounded-lg border p-4 hover:bg-gray-50">
                                        <div class="relative mr-3">
                                            <input type="checkbox" value="{{ $rule->id }}" wire:model="selectedRules"
                                                   class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"/>
                                            <svg
                                                class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                                fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                                            </svg>
                                        </div>
                                        @if ($rule->icon_image)
                                            <img src="{{ Storage::url($rule->icon_image) }}"
                                                 alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                                 class="mr-2 h-8 w-8 object-contain"/>
                                        @endif
                                        <span class="block text-sm font-medium text-gray-900">{{ $rule->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-3 font-semibold">{{ __('Aizliegumi') }}</h3>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($prohibitedRules as $rule)
                                    <label
                                        class="relative flex cursor-pointer items-center justify-start rounded-lg border p-4 hover:bg-gray-50">
                                        <div class="relative mr-3">
                                            <input type="checkbox" value="{{ $rule->id }}" wire:model="selectedRules"
                                                   class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"/>
                                            <svg
                                                class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                                fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                                            </svg>
                                        </div>
                                        @if ($rule->icon_image)
                                            <img src="{{ Storage::url($rule->icon_image) }}"
                                                 alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                                 class="mr-2 h-8 w-8 object-contain"/>
                                        @endif
                                        <span class="block text-sm font-medium text-gray-900">{{ $rule->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 border-l-4 border-yellow-400 bg-yellow-50 p-4">
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
                                    {{ __('Nav pievienoti noteikumi. Lūdzu, vispirms pievieno noteikumus') }}
                                    <a href="{{ route('dashboard.rule.add') }}"
                                       class="font-medium text-yellow-700 underline hover:text-yellow-600">
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
            @if (! ($houseRules->isEmpty() && $safetyRules->isEmpty()))
                <x-btn-primary type="submit" class="">
                    @lang('Saglabāt')
                </x-btn-primary>
            @endif
        </div>
    </form>
</div>
