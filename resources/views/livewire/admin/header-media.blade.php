<div>
    <x-admin.flash-message />
    <form wire:submit="store">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Galvenais karuselis') }}
                </h2>
                @if ($headerMedia->count() > 0)
                    <div class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ __('Esošie faili') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4" wire:sort="updateMediaOrder">
                            @foreach ($headerMedia as $item)
                                <div class="group relative" wire:key="existing-media-{{ $item->id }}"
                                    wire:sort:item="{{ $item->id }}">
                                    @if ($item->isVideo())
                                        <video src="{{ Storage::url($item->filename) }}" muted preload="metadata"
                                            class="h-32 w-full rounded-md border border-gray-300 object-cover bg-black"></video>
                                        <span
                                            class="absolute bottom-2 left-2 rounded bg-black/70 px-2 py-0.5 text-xs font-medium text-white">
                                            {{ __('Video') }}
                                        </span>
                                    @else
                                        <img src="{{ Storage::url($item->filename) }}" alt="{{ __('Galvenes attēls') }}"
                                            class="h-32 w-full rounded-md border border-gray-300 object-cover" />
                                    @endif
                                    <button type="button" wire:click="removeMedia({{ $item->id }})"
                                        wire:confirm="Vai tiešām vēlaties dzēst šo failu?"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 hover:bg-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($media && count($media) > 0)
                    <div class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ __('Jaunie faili') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                            @foreach ($media as $index => $file)
                                <div class="group relative">
                                    <div class="relative">
                                        @if ($this->isMediaVideo($index))
                                            <video src="{{ $file->temporaryUrl() }}" muted preload="metadata"
                                                class="{{ $this->isMediaOversized($index) ? 'border-2 border-red-500' : 'border border-gray-300' }} h-32 w-full rounded-md object-cover bg-black"></video>
                                            <span
                                                class="absolute bottom-2 left-2 rounded bg-black/70 px-2 py-0.5 text-xs font-medium text-white">
                                                {{ __('Video') }}
                                            </span>
                                        @else
                                            <img src="{{ $file->temporaryUrl() }}" alt="{{ __('Priekšskatījums') }}"
                                                class="{{ $this->isMediaOversized($index) ? 'border-2 border-red-500' : 'border border-gray-300' }} h-32 w-full rounded-md object-cover" />
                                        @endif

                                        @if ($this->isMediaOversized($index))
                                            <div
                                                class="absolute top-0 right-0 left-0 rounded-t-md bg-red-500 px-2 py-1 text-xs text-white">
                                                <svg class="mr-1 inline h-3 w-3" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $this->getMediaSizeInKB($index) }}
                                                KB
                                            </div>
                                        @else
                                            <div
                                                class="absolute top-0 left-0 rounded-tl-md bg-green-500 px-2 py-1 text-xs text-white">
                                                {{ $this->getMediaSizeInKB($index) }}
                                                KB
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button" wire:click="removeNewMedia({{ $index }})"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 hover:bg-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-5">
                    <label class="mb-2 block text-sm/6 font-medium text-gray-900">
                        {{ __('Pievienot attēlus vai video') }}
                    </label>
                    <div class="flex w-full items-center justify-center">
                        <label for="header-media-upload"
                            class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition-colors duration-200 hover:bg-gray-100"
                            wire:loading.class="pointer-events-none cursor-not-allowed bg-gray-100 opacity-75"
                            wire:loading.class.remove="cursor-pointer bg-gray-50 hover:bg-gray-100" wire:target="media">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div wire:loading wire:target="media" class="flex flex-col items-center">
                                    <svg class="mb-2 h-8 w-8 animate-spin text-gray-600" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-600">
                                        {{ __('Augšupielādē...') }}
                                    </p>
                                </div>

                                <div wire:loading.remove wire:target="media">
                                    <svg class="mb-4 h-8 w-8 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">
                                            {{ __('Noklikšķiniet, lai augšupielādētu') }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('PNG, JPG vai JPEG (Maks. 512 KB), MP4 video (Maks. 15 MB)') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('Varat izvēlēties vairākus failus') }}
                                    </p>
                                </div>
                            </div>
                            <input id="header-media-upload" type="file" wire:model="media" class="hidden"
                                accept="image/*,video/mp4" multiple wire:loading.attr="disabled" wire:target="media" />
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('media')" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('media.*'))->flatten()" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <x-btn-primary type="submit" class="">
                @lang('Saglabāt')
            </x-btn-primary>
        </div>
    </form>
</div>
