<div>
    <x-admin.flash-message />
    <form wire:submit="save">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
                    {{ __('Galvenes virsraksts') }}
                </h2>
                <p class="mb-6 text-sm text-gray-500">
                    {{ __('Galvenais virsraksts virs galvenes karuseļa. Ievadiet tekstu katrā valodā.') }}
                </p>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="title-lv" class="block text-sm/6 font-medium text-gray-900">
                            {{ __('Latviešu') }}
                        </label>
                        <div class="mt-2">
                            <input
                                id="title-lv"
                                type="text"
                                wire:model="titleLv"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('titleLv')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="title-en" class="block text-sm/6 font-medium text-gray-900">
                            {{ __('Angļu') }}
                        </label>
                        <div class="mt-2">
                            <input
                                id="title-en"
                                type="text"
                                wire:model="titleEn"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('titleEn')" class="mt-2" />
                        </div>
                    </div>
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
