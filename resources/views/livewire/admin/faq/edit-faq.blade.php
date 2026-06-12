<div>
    <x-admin.flash-message />
    <form wire:submit="save">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Rediģēt jautājumu') }}
                </h2>
                <div class="mt-5">
                    <label for="question" class="block text-sm/6 font-medium text-gray-900">{{ __('Jautājums') }}</label>
                    <div class="mt-2">
                        <input
                            id="question"
                            type="text"
                            wire:model="question"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('question')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-5">
                    <label class="block text-sm/6 font-medium text-gray-900">{{ __('Atbilde') }}</label>
                    <div class="mt-2" wire:ignore>
                        <textarea id="faq-answer-editor" rows="6"></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('answer')" class="mt-2" />
                </div>

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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
                                </svg>
                            </span>
                            <span>{{ __('Rādīt mājas lapā?') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('dashboard.faqs') }}" class="text-sm/6 font-semibold text-gray-900">
                    @lang('Atcelt')
                </a>
                <x-btn-primary type="submit" class="">
                    @lang('Saglabāt')
                </x-btn-primary>
            </div>
        </div>
    </form>
</div>

@script
<script>
    const faqEditorConfig = {
        toolbar: {
            items: [
                'undo',
                'redo',
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
        initialData: @js($answer ?? ''),
        licenseKey: 'GPL',
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
        },
    };

    ClassicEditor.create(document.querySelector('#faq-answer-editor'), faqEditorConfig)
        .then((newEditor) => {
            newEditor.model.document.on('change:data', () => {
                $wire.set('answer', newEditor.getData());
            });
        })
        .catch((error) => {
            console.error('CKEditor (edit-faq) initialization error:', error);
        });
</script>
@endscript
