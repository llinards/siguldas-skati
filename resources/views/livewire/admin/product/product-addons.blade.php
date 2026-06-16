<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Papildinājumi') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-3 rounded-lg border p-4">
        <h2 class="font-medium">{{ $editingId ? __('Rediģēt papildinājumu') : __('Pievienot papildinājumu') }}</h2>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1 block text-sm">{{ __('Nosaukums (LV)') }}</label>
                <input type="text" wire:model="nameLv" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('nameLv')" class="mt-1" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Nosaukums (EN)') }}</label>
                <input type="text" wire:model="nameEn" class="w-full rounded-lg border p-2" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Apraksts (LV)') }}</label>
                <textarea wire:model="descLv" rows="2" class="w-full rounded-lg border p-2"></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Apraksts (EN)') }}</label>
                <textarea wire:model="descEn" rows="2" class="w-full rounded-lg border p-2"></textarea>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="isActive" /> {{ __('Aktīvs') }}
        </label>

        <div class="flex gap-2">
            <button type="button" wire:click="save" class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Saglabāt') }}</button>
            @if ($editingId)
                <button type="button" wire:click="resetForm" class="rounded-full border px-5 py-2">{{ __('Atcelt') }}</button>
            @endif
        </div>
    </div>

    <div class="space-y-2">
        @forelse ($addons as $addon)
            <div class="flex items-center justify-between rounded-lg border p-3">
                <div>
                    <p class="font-medium">{{ $addon->getTranslation('name', 'lv') }}</p>
                    <p class="text-sm text-neutral-500">{{ $addon->getTranslation('description', 'lv') }}</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <button type="button" wire:click="toggleActive({{ $addon->id }})"
                        class="{{ $addon->is_active ? 'text-green-700' : 'text-neutral-400' }}">
                        {{ $addon->is_active ? __('Aktīvs') : __('Neaktīvs') }}
                    </button>
                    <button type="button" wire:click="edit({{ $addon->id }})" class="text-blue-600 underline">{{ __('Rediģēt') }}</button>
                    <button type="button" wire:click="delete({{ $addon->id }})"
                        wire:confirm="{{ __('Dzēst šo papildinājumu?') }}" class="text-red-600 underline">{{ __('Dzēst') }}</button>
                </div>
            </div>
        @empty
            <p class="text-sm text-neutral-500">{{ __('Nav papildinājumu.') }}</p>
        @endforelse
    </div>
</div>
