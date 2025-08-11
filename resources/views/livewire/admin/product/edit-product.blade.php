<div>
    <x-admin.flash-message/>
    <form wire:submit="update">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Rediģēt') }} - {{ $product->title }}
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
                <div class="mt-5 flex flex-col sm:flex-row sm:space-x-6">
                    <div class="sm:flex-1/3">
                        <label for="title" class="block text-sm/6 font-medium text-gray-900">Nosaukums</label>
                        <div class="mt-2">
                            <input
                                type="text"
                                wire:model="title"
                                autocomplete="given-name"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('title')" class="mt-2"/>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0">
                        <label for="personCount" class="block text-sm/6 font-medium text-gray-900">
                            Cilvēku skaits
                        </label>
                        <div class="mt-2">
                            <input
                                type="number"
                                wire:model="personCount"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('personCount')" class="mt-2"/>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0">
                        <label for="childrenCount" class="block text-sm/6 font-medium text-gray-900">
                            Bērnu skaits
                        </label>
                        <div class="mt-2">
                            <input
                                type="number"
                                wire:model="childrenCount"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('childrenCount')" class="mt-2"/>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="cover" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Attēls') }}
                    </label>
                    <div class="mt-2">
                        <div class="flex flex-col sm:flex-row sm:space-x-6">
                            @if (! $cover && $product->cover)
                                <div>
                                    <img
                                        src="{{ Storage::url($product->cover) }}"
                                        alt="{{ __('Pašreizējais attēls') }}"
                                        class="h-32 w-auto rounded-md border border-gray-300 object-cover"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('Pašreizējais attēls') }}
                                    </p>
                                </div>
                            @endif

                            @if ($cover)
                                <div>
                                    <img
                                        src="{{ $cover->temporaryUrl() }}"
                                        alt="{{ __('Priekšskatījums') }}"
                                        class="h-32 w-auto rounded-md border border-gray-300 object-cover"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('Jauns attēls') }}
                                    </p>
                                </div>
                            @endif

                            <div class="mt-5 flex w-full items-start justify-center sm:mt-0">
                                <label
                                    for="cover-upload"
                                    class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition-colors duration-200 hover:bg-gray-100"
                                    wire:loading.class="pointer-events-none cursor-not-allowed bg-gray-100 opacity-75"
                                    wire:loading.class.remove="cursor-pointer bg-gray-50 hover:bg-gray-100"
                                    wire:target="cover"
                                >
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <!-- Loading indicator -->
                                        <div wire:loading wire:target="cover" class="flex flex-col items-center">
                                            <svg
                                                class="text-gray-600-600 mb-2 h-8 w-8 animate-spin"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-600">
                                                {{ __('Augšupielādē...') }}
                                            </p>
                                        </div>

                                        <!-- Default state -->
                                        <div wire:loading.remove wire:target="cover">
                                            @if (! $cover)
                                                <svg
                                                    class="mb-4 h-8 w-8 text-gray-500"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 16"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                    />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500">
                                                    <span class="font-semibold">
                                                        {{ __('Noklikšķiniet, lai augšupielādētu') }}
                                                    </span>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('PNG, JPG vai JPEG (Maks. 512 KB)') }}
                                                </p>
                                            @else
                                                <svg
                                                    class="mb-2 h-8 w-8 text-green-500"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 13l4 4L19 7"
                                                    ></path>
                                                </svg>
                                                <p class="text-sm font-medium text-green-600">
                                                    {{ __('Attēls augšupielādēts') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Noklikšķiniet, lai mainītu') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input
                                        id="cover-upload"
                                        type="file"
                                        wire:model="cover"
                                        class="hidden"
                                        accept="image/*"
                                        wire:loading.attr="disabled"
                                        wire:target="cover"
                                    />
                                </label>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('cover')" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="description" class="block text-sm/6 font-medium text-gray-900">Apraksts</label>
                    <div class="mt-2">
                        <textarea
                            rows="3"
                            wire:model="description"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="pricelist" class="block text-sm/6 font-medium text-gray-900">Cenrādis</label>
                    <div class="mt-2" wire:ignore>
                        <textarea id="pricelist-editor" rows="3" class=""></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('pricelist')" class="mt-2"/>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-sm/6 font-semibold text-gray-900">
                @lang('Atpakaļ')
            </a>
            <x-btn-primary type="submit" class="">
                @lang('Saglabāt')
            </x-btn-primary>
        </div>
    </form>
</div>

@script
<script>
    const LICENSE_KEY = 'GPL';
    const editorConfig = {
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
                'code',
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
        initialData: @js($pricelist ?? ''),
        licenseKey: LICENSE_KEY,
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
        },
        placeholder: 'Type or paste your content here!',
    };

    let editor;

    ClassicEditor.create(document.querySelector('#pricelist-editor'), editorConfig)
        .then((newEditor) => {
            editor = newEditor;

            // Sync CKEditor content with Livewire on every change
            editor.model.document.on('change:data', () => {
                $wire.set('pricelist', editor.getData());
            });
        })
        .catch((error) => {
            console.error('CKEditor initialization error:', error);
        });
</script>
@endscript
