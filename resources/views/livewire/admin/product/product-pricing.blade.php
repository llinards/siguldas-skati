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
</div>
