<div>
    <x-admin.flash-message/>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-h-sm-mob lg:text-h-mob leading-none">
            {{ __('Galerijas') }}
        </h2>
    </div>

    <!-- Galleries Grid -->
    <div wire:sortable="updateGalleryOrder" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($galleries as $gallery)
            <div
                class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                wire:key="gallery-{{ $gallery->id }}"
                wire:sortable.item="{{ $gallery->id }}"
            >
                <!-- Gallery Image Placeholder -->
                <div class="relative aspect-square bg-gray-200">
                    <div class="flex h-full w-full items-center justify-center">
                        @if ($gallery->images->first())
                            <img
                                src="{{ Storage::url($gallery->images->first()->filename) }}"
                                alt="{{ $gallery->title }}"
                                class="h-full w-full object-cover"
                            />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-2 right-2">
                        <span
                            class="{{ $gallery->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                        >
                            {{ $gallery->is_active ? __('Aktīvs') : __('Neaktīvs') }}
                        </span>
                    </div>
                </div>

                <!-- Gallery Info -->
                <div class="p-4">
                    <h3 class="mb-2 truncate text-lg font-semibold text-gray-900" title="{{ $gallery->title }}">
                        {{ $gallery->title }}
                    </h3>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2">
                        <!-- Toggle Active Status -->
                        <button
                            wire:click="toggleActive({{ $gallery->id }})"
                            class="{{ $gallery->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} inline-flex items-center rounded-md px-3 py-1 text-sm font-medium transition-colors duration-200"
                        >
                            {{ $gallery->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <div class="flex gap-2">
                            <!-- Edit Button -->
                            <a
                                href="{{ route('dashboard.gallery.edit', $gallery) }}"
                                class="text-bg-ss-600 inline-flex items-center rounded-md p-2 transition-colors duration-200 hover:bg-gray-50 hover:text-gray-900"
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
                                wire:click="deleteGallery({{ $gallery->id }})"
                                wire:confirm="{{ __('Vai tiešām dzēst?') }}"
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
                    {{ __('Nav nevienas galerijas.') }}
                </p>
            </div>
        @endforelse
    </div>

    <!-- Add Gallery Button -->
    <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-end">
        <x-btn-primary :href="route('dashboard.gallery.add')">
            {{ __('Pievienot galeriju') }}
        </x-btn-primary>
    </div>
</div>
