<div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
    @if ($bookingError)
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $bookingError }}</div>
    @endif

    <div class="grid grid-cols-2 gap-3">
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Reģistrēšanās') }}
            <input type="date" wire:model.live="checkIn" class="mt-1 w-full rounded-lg border-neutral-300" />
            @error('checkIn') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </label>
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Izrakstīšanās') }}
            <input type="date" wire:model.live="checkOut" class="mt-1 w-full rounded-lg border-neutral-300" />
            @error('checkOut') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </label>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Pieaugušie') }}
            <input type="number" min="1" wire:model.live="adults" class="mt-1 w-full rounded-lg border-neutral-300" />
        </label>
        <label class="text-sm font-medium text-neutral-700">
            {{ __('Bērni') }}
            <input type="number" min="0" wire:model.live="children" class="mt-1 w-full rounded-lg border-neutral-300" />
        </label>
    </div>

    @if ($addons->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($addons as $addon)
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" wire:model.live="selectedAddons.{{ $addon->id }}" />
                    {{ $addon->getTranslation('name', app()->getLocale()) }}
                    <span class="ml-auto">€{{ number_format($addon->price / 100, 2) }}</span>
                </label>
            @endforeach
        </div>
    @endif

    @if ($quote)
        <div class="mt-4 space-y-1 border-t border-neutral-200 pt-4 text-sm">
            <div class="flex justify-between">
                <span>€{{ number_format(($quote->nightsTotal / max($quote->nights, 1)) / 100, 0) }} × {{ $quote->nights }} {{ __('naktis') }}</span>
                <span>€{{ number_format($quote->nightsTotal / 100, 2) }}</span>
            </div>
            @if ($quote->cleaningFee > 0)
                <div class="flex justify-between"><span>{{ __('Uzkopšana') }}</span><span>€{{ number_format($quote->cleaningFee / 100, 2) }}</span></div>
            @endif
            @if ($quote->addonsTotal > 0)
                <div class="flex justify-between"><span>{{ __('Papildpakalpojumi') }}</span><span>€{{ number_format($quote->addonsTotal / 100, 2) }}</span></div>
            @endif
            <div class="flex justify-between border-t border-neutral-200 pt-2 font-semibold">
                <span>{{ __('Kopā') }}</span><span>€{{ number_format($quote->grandTotal / 100, 2) }}</span>
            </div>
        </div>
    @endif

    <div class="mt-4 space-y-2">
        <input type="text" wire:model="guestName" placeholder="{{ __('Vārds, uzvārds') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        <input type="email" wire:model="guestEmail" placeholder="{{ __('E-pasts') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        <input type="text" wire:model="guestPhone" placeholder="{{ __('Tālrunis') }}" class="w-full rounded-lg border-neutral-300" />
        @error('guestPhone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </div>

    <button type="button" wire:click="reserve" wire:loading.attr="disabled"
        class="mt-4 w-full rounded-xl bg-[#2f3a1f] px-4 py-3 font-semibold text-white hover:opacity-90">
        {{ __('Rezervēt brīvdienu māju') }}
    </button>
</div>
