<div>
    <x-admin.flash-message/>
    <form wire:submit="update">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="mb-3 leading-none text-h-sm-mob lg:text-h-mob">{{ __('Rediģēt') }}
                    - {{$product->title}}</h2>

                <div class="mt-10">
                    <div>
                        <label for="title" class="block text-sm/6 font-medium text-gray-900">Nosaukums</label>
                        <div class="mt-2">
                            <input type="text" wire:model="title"
                                   autocomplete="given-name"
                                   class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"/>
                            <x-input-error :messages="$errors->get('title')" class="mt-2"/>
                        </div>
                    </div>

                    <div class="mt-1">
                        <div class="block mt-4">
                            <label class="mt-4 self-start flex text-sm text-gray-900 space-x-2 cursor-pointer">
                        <span class="relative">
                            <input wire:model="is_active"
                                   type="checkbox"
                                   class="peer appearance-none h-5 w-5 border-1 border-ss-dark rounded bg-white checked:bg-ss-dark checked:border-ss-dark transition duration-200">
                            <svg
                                class="pointer-events-none absolute left-0 top-0 h-5 w-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-150"
                                fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                            </svg>
                        </span>
                                <span>{{ __('Rādīt mājas lapā?') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="block text-sm/6 font-medium text-gray-900">Adrese</label>
                        <div class="mt-2">
                            <div
                                class="flex items-center rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
                                <div
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-500  outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    {{ url('/') }}/{{app()->getLocale()}}/{{$product->slug}}
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mt-5">
                        <label for="description" class="block text-sm/6 font-medium text-gray-900">Apraksts</label>
                        <div class="mt-2">
                        <textarea rows="3" wire:model="description"
                                  class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href={{route('dashboard')}} wire:navigate
               class="text-sm/6 font-semibold text-gray-900">@lang('Atpakaļ')</a>
            <x-btn-primary type="submit" class="">@lang('Saglabāt')</x-btn-primary>
        </div>
    </form>
</div>
