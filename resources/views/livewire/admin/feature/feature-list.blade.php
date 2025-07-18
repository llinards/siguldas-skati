<div>
    <x-admin.flash-message />
    <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Ērtības') }}</h2>
    <div wire:sortable="updateFeatureOrder" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        @forelse ($features as $feature)
            <div
                class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                wire:key="feature-{{ $feature->id }}"
                wire:sortable.item="{{ $feature->id }}"
            >
                <!-- Feature Icon -->
                <div class="flex items-center justify-center bg-gray-50 p-6" wire:sortable.handle>
                    @if ($feature->icon_image)
                        <img
                            src="{{ Storage::url($feature->icon_image) }}"
                            alt="{{ $feature->getTranslation('title', app()->getLocale()) }}"
                            class="h-16 w-16 object-contain"
                        />
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-200">
                            <span class="text-lg text-gray-500">{{ __('Nav') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Feature Info -->
                <div class="p-4">
                    <h3 class="mb-2 truncate text-lg font-semibold text-gray-900" title="{{ $feature->title }}">
                        {{ $feature->getTranslation('title', app()->getLocale()) }}
                    </h3>
                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2">
                        <!-- Toggle Active Status -->
                        <button
                            wire:click="toggleActive({{ $feature->id }})"
                            class="{{ $feature->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} inline-flex items-center rounded-md px-3 py-1 text-sm font-medium transition-colors duration-200"
                        >
                            {{ $feature->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <div class="flex gap-2">
                            <!-- Edit Button -->
                            <a
                                href="{{ route('dashboard.feature.edit', $feature->id) }}"
                                class="inline-flex items-center rounded-md p-2 text-indigo-600 transition-colors duration-200 hover:bg-indigo-50 hover:text-indigo-900"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    ></path>
                                </svg>
                            </a>

                            <!-- Delete Button -->
                            <button
                                wire:click="deleteFeature({{ $feature->id }})"
                                wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo funkciju?') }}"
                                class="inline-flex items-center rounded-md p-2 text-red-600 transition-colors duration-200 hover:bg-red-50 hover:text-red-900"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p class="text-base leading-7.5 md:text-xl xl:text-2xl">
                    @lang('Nav pievienotas ērtības!')
                </p>
            </div>
        @endforelse
    </div>
    <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-end">
        <x-btn-primary :href="route('dashboard.feature.add')">
            {{ __('Pievienot jaunu ērtību') }}
        </x-btn-primary>
    </div>
</div>
