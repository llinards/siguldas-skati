<div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
    @if ($bookingError)
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $bookingError }}</div>
    @endif

    <div>
        <span class="text-sm font-medium text-neutral-700">{{ __('Reģistrēšanās') }} – {{ __('Izrakstīšanās') }}</span>
        <div wire:ignore
            x-data="bookingCalendar({
                minDate: @js(now()->toDateString()),
                disabled: @js($unavailableDates),
                selected: @js(array_values(array_filter([$checkIn, $checkOut]))),
            })">
            <input x-ref="input" type="text" readonly
                placeholder="{{ __('Izvēlies datumus') }}"
                class="mt-1 w-full cursor-pointer rounded-lg border-neutral-300 focus:border-ss-dark focus:ring-ss-dark" />
        </div>
        @error('checkIn') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        @error('checkOut') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </div>

    <div class="mt-3">
        <span class="text-sm font-medium text-neutral-700">{{ __('Viesi') }}</span>
        <div class="mt-1 rounded-lg border border-neutral-300 px-4 py-1">
            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="font-medium text-neutral-800">{{ __('Pieaugušie') }}</p>
                    <p class="text-xs text-neutral-500">{{ __('Maks.') }} {{ $product->person_count }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="decrementAdults" @disabled($adults <= 1)
                        class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none disabled:opacity-40">−</button>
                    <span class="w-5 text-center tabular-nums">{{ $adults }}</span>
                    <button type="button" wire:click="incrementAdults"
                        class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none">+</button>
                </div>
            </div>

            @if ($product->children_count > 0)
                <div class="flex items-center justify-between border-t border-neutral-100 py-2">
                    <div>
                        <p class="font-medium text-neutral-800">{{ __('Bērni') }}</p>
                        <p class="text-xs text-neutral-500">{{ __('Maks.') }} {{ $product->children_count }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="decrementChildren" @disabled($children <= 0)
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none disabled:opacity-40">−</button>
                        <span class="w-5 text-center tabular-nums">{{ $children }}</span>
                        <button type="button" wire:click="incrementChildren"
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none">+</button>
                    </div>
                </div>
            @endif
        </div>
        @if ($guestError)
            <p class="mt-1 text-xs text-red-600">{{ $guestError }}</p>
        @endif
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
