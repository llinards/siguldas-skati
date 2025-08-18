<div>
    <x-admin.flash-message/>
    <form wire:submit="update">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Rediģēt galeriju') }} - {{ $gallery->title }}
                </h2>
                <div class="mt-5">
                    <div class="block">
                        <label class="flex cursor-pointer space-x-2 self-start text-sm text-gray-900">
                            <span class="relative">
                                <input
                                    wire:model="is_active"
                                    type="checkbox"
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
                            </span>
                            <span>{{ __('Rādīt mājas lapā?') }}</span>
                        </label>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="sm:flex-1">
                        <label for="title"
                               class="block text-sm/6 font-medium text-gray-900">{{ __('Nosaukums') }}</label>
                        <div class="mt-2">
                            <input
                                type="text"
                                wire:model="title"
                                autocomplete="title"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('title')" class="mt-2"/>
                        </div>
                    </div>
                </div>

                @if($gallery->images->count() > 0)
                    <div class="mt-5">
                        <label class="block text-sm/6 font-medium text-gray-900">
                            {{ __('Galerijas attēli') }}
                        </label>
                        <div class="mt-2">
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                                @foreach($gallery->images->take(6) as $image)
                                    <div class="relative">
                                        <img
                                            src="{{ Storage::url($image->filename) }}"
                                            alt="{{ __('Galerijas attēls') }}"
                                            class="h-24 w-full rounded-md border border-gray-300 object-cover"
                                        />
                                    </div>
                                @endforeach

                                @if($gallery->images->count() > 6)
                                    <div
                                        class="flex h-24 w-full items-center justify-center rounded-md border border-gray-300 bg-gray-50">
                                        <span class="text-sm text-gray-500">
                                            +{{ $gallery->images->count() - 6 }} {{ __('vēl') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <x-btn-primary href="{{ route('dashboard.gallery.images', $gallery) }}">
                                    {{ __('Pārvaldīt attēlus') }}
                                </x-btn-primary>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-5">
                        <label class="block text-sm/6 font-medium text-gray-900">
                            {{ __('Galerijas attēli') }}
                        </label>
                        <div class="mt-2">
                            <div class="rounded-md border-2 border-dashed border-gray-300 p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"
                                    />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">{{ __('Nav pievienotu attēlu') }}</p>
                                <a
                                    href="{{ route('dashboard.gallery.images', $gallery) }}"
                                    class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                                >
                                    {{ __('Pievienot attēlus') }} →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('dashboard.galleries') }}" wire:navigate class="text-sm/6 font-semibold text-gray-900">
                @lang('Atpakaļ')
            </a>
            <x-btn-primary type="submit" class="">
                @lang('Saglabāt')
            </x-btn-primary>
        </div>
    </form>
</div>
