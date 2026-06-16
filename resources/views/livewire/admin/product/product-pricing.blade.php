<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Cenas') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-4">
        <div>
            <label class="mb-1 block font-medium" for="basePrice">{{ __('Pamatcena (EUR/nakts)') }}</label>
            <input id="basePrice" type="number" step="0.01" min="0" wire:model="basePrice"
                class="w-full rounded-lg border p-2" />
            <x-input-error :messages="$errors->get('basePrice')" class="mt-1" />
        </div>

        <div>
            <label class="mb-1 block font-medium" for="minNights">{{ __('Minimālais nakšu skaits') }}</label>
            <input id="minNights" type="number" min="1" wire:model="minNights"
                class="w-full rounded-lg border p-2" />
            <x-input-error :messages="$errors->get('minNights')" class="mt-1" />
        </div>

        <button type="button" wire:click="saveBaseSettings"
            class="rounded-full bg-ss-dark px-6 py-2 text-white">{{ __('Saglabāt') }}</button>
    </div>

    <hr class="border-neutral-200" />

    <div class="space-y-4">
        <h2 class="font-medium">{{ __('Cenas pa datumiem') }}</h2>

        <div wire:ignore x-data="pricingCalendar({ minDate: '{{ now()->toDateString() }}' })">
            <div x-ref="calendar"></div>
        </div>

        <div class="flex items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium" for="overridePrice">{{ __('Cena izvēlētajiem (EUR/nakts)') }}</label>
                <input id="overridePrice" type="number" step="0.01" min="0" wire:model="overridePrice"
                    class="w-40 rounded-lg border p-2" />
            </div>
            <button type="button" wire:click="applyPriceToSelected"
                class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Piemērot izvēlētajiem') }}</button>
        </div>
        <x-input-error :messages="$errors->get('overridePrice')" />
        <x-input-error :messages="$errors->get('selectedDates')" />

        @if ($overrides->isNotEmpty())
            <table class="min-w-full divide-y text-sm">
                <thead class="text-left">
                    <tr><th class="py-2">{{ __('Datums') }}</th><th class="py-2">{{ __('Cena') }}</th><th></th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($overrides as $override)
                        <tr>
                            <td class="py-2">{{ $override->date->format('d.m.Y') }}</td>
                            <td class="py-2">{{ number_format($override->price / 100, 2) }} €</td>
                            <td class="py-2 text-right">
                                <button type="button" wire:click="removeOverride({{ $override->id }})"
                                    class="text-red-600 underline">{{ __('Dzēst') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-sm text-neutral-500">{{ __('Nav cenu korekciju.') }}</p>
        @endif
    </div>
</div>
