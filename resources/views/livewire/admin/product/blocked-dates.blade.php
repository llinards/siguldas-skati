<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('Bloķētie datumi') }}</h1>
        <p class="text-neutral-600">{{ $product->getTranslation('title', app()->getLocale()) }}</p>
    </div>

    <div class="space-y-3 rounded-lg border p-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="mb-1 block text-sm">{{ __('No') }}</label>
                <input type="date" wire:model="startDate" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('startDate')" class="mt-1" />
            </div>
            <div>
                <label class="mb-1 block text-sm">{{ __('Līdz') }}</label>
                <input type="date" wire:model="endDate" class="w-full rounded-lg border p-2" />
                <x-input-error :messages="$errors->get('endDate')" class="mt-1" />
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm">{{ __('Iemesls') }}</label>
            <input type="text" wire:model="reason" class="w-full rounded-lg border p-2" />
        </div>
        <button type="button" wire:click="addBlock" class="rounded-full bg-ss-dark px-5 py-2 text-white">{{ __('Bloķēt datumus') }}</button>
    </div>

    <div class="space-y-2">
        @forelse ($blocks as $block)
            <div class="flex items-center justify-between rounded-lg border p-3 text-sm">
                <div>
                    <span class="font-medium">{{ $block->start_date->format('d.m.Y') }} – {{ $block->end_date->format('d.m.Y') }}</span>
                    @if ($block->reason)<span class="text-neutral-500"> · {{ $block->reason }}</span>@endif
                </div>
                <button type="button" wire:click="removeBlock({{ $block->id }})"
                    wire:confirm="{{ __('Noņemt šo bloķējumu?') }}" class="text-red-600 underline">{{ __('Dzēst') }}</button>
            </div>
        @empty
            <p class="text-sm text-neutral-500">{{ __('Nav bloķētu datumu.') }}</p>
        @endforelse
    </div>
</div>
