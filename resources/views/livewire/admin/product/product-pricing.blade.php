<div>
    <x-admin.flash-message />
    <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">
        {{ __('Cenas') }} - {{ $product->getTranslation('title', app()->getLocale()) }}
    </h2>

    <div class="space-y-12">
        {{-- Base price & minimum nights --}}
        <div class="border-b border-gray-900/10 pb-12">
            <div class="flex flex-col sm:flex-row sm:space-x-6">
                <div class="sm:flex-1/3">
                    <label for="basePrice" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Pamatcena (EUR/nakts)') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="basePrice"
                            type="number"
                            step="0.01"
                            min="0"
                            wire:model="basePrice"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('basePrice')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-5 sm:mt-0">
                    <label for="minNights" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Minimālais nakšu skaits') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="minNights"
                            type="number"
                            min="1"
                            wire:model="minNights"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('minNights')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <x-btn-primary type="button" wire:click="saveBaseSettings">
                    @lang('Saglabāt')
                </x-btn-primary>
            </div>
        </div>

        {{-- Per-date price overrides --}}
        <div class="border-b border-gray-900/10 pb-12">
            <h3 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Cenas pa datumiem') }}</h3>
            <p class="mb-5 text-xs text-gray-500">
                {{ __('Izvēlies datumus kalendārā un piemēro tiem citu cenu par nakti.') }}
            </p>

            <div wire:ignore x-data="pricingCalendar({ minDate: '{{ now()->toDateString() }}' })">
                <div x-ref="calendar"></div>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:gap-6">
                <div class="sm:w-60">
                    <label for="overridePrice" class="block text-sm/6 font-medium text-gray-900">
                        {{ __('Cena izvēlētajiem (EUR/nakts)') }}
                    </label>
                    <div class="mt-2">
                        <input
                            id="overridePrice"
                            type="number"
                            step="0.01"
                            min="0"
                            wire:model="overridePrice"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                    </div>
                </div>
                <x-btn-primary type="button" wire:click="applyPriceToSelected">
                    @lang('Piemērot izvēlētajiem')
                </x-btn-primary>
            </div>
            <x-input-error :messages="$errors->get('overridePrice')" class="mt-2" />
            <x-input-error :messages="$errors->get('selectedDates')" class="mt-2" />

            <div class="mt-8 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                {{ __('Datums') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                {{ __('Cena') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                <span class="sr-only">{{ __('Darbības') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($overrides as $override)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                                    {{ $override->date->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                    {{ number_format($override->price / 100, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                    <button
                                        type="button"
                                        wire:click="removeOverride({{ $override->id }})"
                                        wire:confirm="{{ __('Dzēst šo cenas korekciju?') }}"
                                        class="inline-flex items-center rounded-md p-2 text-red-600 transition-colors duration-200 hover:bg-red-50 hover:text-red-900"
                                        title="{{ __('Dzēst') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <p class="text-sm text-gray-500">{{ __('Nav cenu korekciju.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Blocked dates --}}
        <div class="border-b border-gray-900/10 pb-12">
            <h3 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Bloķētie datumi') }}</h3>
            <p class="mb-5 text-xs text-gray-500">
                {{ __('Bloķētajos datumos māju nevar rezervēt (piem., apkope vai privāta lietošana).') }}
            </p>

            <div class="flex flex-col gap-4 sm:flex-row sm:space-x-6">
                <div>
                    <label for="startDate" class="block text-sm/6 font-medium text-gray-900">{{ __('No') }}</label>
                    <div class="mt-2">
                        <input
                            id="startDate"
                            type="date"
                            wire:model="startDate"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('startDate')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-5 sm:mt-0">
                    <label for="endDate" class="block text-sm/6 font-medium text-gray-900">{{ __('Līdz') }}</label>
                    <div class="mt-2">
                        <input
                            id="endDate"
                            type="date"
                            wire:model="endDate"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('endDate')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-5 sm:mt-0 sm:flex-1">
                    <label for="reason" class="block text-sm/6 font-medium text-gray-900">{{ __('Iemesls') }}</label>
                    <div class="mt-2">
                        <input
                            id="reason"
                            type="text"
                            wire:model="reason"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                        />
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <x-btn-primary type="button" wire:click="addBlock">
                    @lang('Bloķēt datumus')
                </x-btn-primary>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                {{ __('Periods') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                {{ __('Iemesls') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                <span class="sr-only">{{ __('Darbības') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($blocks as $block)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                                    {{ $block->start_date->format('d.m.Y') }} – {{ $block->end_date->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                    {{ $block->reason ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                    <button
                                        type="button"
                                        wire:click="removeBlock({{ $block->id }})"
                                        wire:confirm="{{ __('Noņemt šo bloķējumu?') }}"
                                        class="inline-flex items-center rounded-md p-2 text-red-600 transition-colors duration-200 hover:bg-red-50 hover:text-red-900"
                                        title="{{ __('Dzēst') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <p class="text-sm text-gray-500">{{ __('Nav bloķētu datumu.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
