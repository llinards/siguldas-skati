<div>
    <x-admin.flash-message />
    <form wire:submit="save">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Sadaļa "Par mums"') }}
                </h2>
                <p class="mb-6 text-sm text-gray-500">
                    {{ __('Saturs sadaļai "Par mums" sākumlapā.') }}
                </p>

                {{-- Title --}}
                <div class="mb-6">
                    <label for="about-title" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Virsraksts') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="about-title"
                            type="text"
                            wire:model="title"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                </div>

                {{-- Subtitle --}}
                <div class="mb-6">
                    <label for="about-subtitle" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Apakšvirsraksts') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="about-subtitle"
                            type="text"
                            wire:model="subtitle"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('subtitle')" class="mt-2" />
                    </div>
                </div>

                {{-- Heading --}}
                <div class="mb-6">
                    <label for="about-heading" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Sadaļas virsraksts') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="about-heading"
                            type="text"
                            wire:model="heading"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('heading')" class="mt-2" />
                    </div>
                </div>

                {{-- Image --}}
                <div class="mb-6">
                    <label class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Attēls') }}
                    </label>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Attēls ir kopīgs visām valodām.') }}</p>
                    <div class="mt-2 flex flex-col sm:flex-row sm:space-x-6">
                        @if ($image)
                            <div class="mb-4">
                                <img
                                    src="{{ $image->temporaryUrl() }}"
                                    alt="{{ __('Priekšskatījums') }}"
                                    class="h-32 w-auto rounded-md border border-gray-300 object-cover"
                                />
                            </div>
                        @elseif ($currentImage)
                            <div class="mb-4">
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($currentImage) }}"
                                    alt="{{ __('Pašreizējais attēls') }}"
                                    class="h-32 w-auto rounded-md border border-gray-300 object-cover"
                                />
                            </div>
                        @endif

                        <div class="mt-5 flex w-full items-start justify-center sm:mt-0">
                            <label
                                for="about-image-upload"
                                class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition-colors duration-200 hover:bg-gray-100"
                                wire:loading.class="pointer-events-none cursor-not-allowed bg-gray-100 opacity-75"
                                wire:loading.class.remove="cursor-pointer bg-gray-50 hover:bg-gray-100"
                                wire:target="image"
                            >
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div wire:loading wire:target="image" class="flex flex-col items-center">
                                        <svg class="mb-2 h-8 w-8 animate-spin text-gray-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600">{{ __('Augšupielādē...') }}</p>
                                    </div>

                                    <div wire:loading.remove wire:target="image">
                                        @if (! $image)
                                            <svg class="mb-4 h-8 w-8 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">{{ __('Noklikšķiniet, lai augšupielādētu') }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500">{{ __('PNG, JPG vai JPEG (Maks. 512 KB)') }}</p>
                                        @else
                                            <svg class="mb-2 h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-green-600">{{ __('Attēls augšupielādēts') }}</p>
                                            <p class="text-xs text-gray-500">{{ __('Noklikšķiniet, lai mainītu') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <input
                                    id="about-image-upload"
                                    type="file"
                                    wire:model="image"
                                    class="hidden"
                                    accept="image/*"
                                    wire:loading.attr="disabled"
                                    wire:target="image"
                                />
                            </label>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm/6 font-medium text-gray-900">{{ __('Apraksts') }}</label>
                    <div class="mt-2" wire:ignore>
                        <textarea id="about-description-editor" rows="6"></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <x-btn-primary type="submit">
                @lang('Saglabāt')
            </x-btn-primary>
        </div>
    </form>
</div>

@script
<script>
    const aboutEditorConfig = {
        toolbar: {
            items: [
                'undo',
                'redo',
                '|',
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                '|',
                'link',
                '|',
                'bulletedList',
                'numberedList',
            ],
            shouldNotGroupWhenFull: false,
        },
        plugins: [Autosave, Bold, Heading, Essentials, Italic, Link, List, Paragraph, Strikethrough, Underline],
        heading: {
            options: [
                {
                    model: 'paragraph',
                    title: 'Paragraph',
                },
            ],
        },
        initialData: @js($description ?? ''),
        licenseKey: 'GPL',
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
        },
    };

    ClassicEditor.create(document.querySelector('#about-description-editor'), aboutEditorConfig)
        .then((newEditor) => {
            newEditor.model.document.on('change:data', () => {
                $wire.set('description', newEditor.getData());
            });
        })
        .catch((error) => {
            console.error('CKEditor (about-us) initialization error:', error);
        });
</script>
@endscript
