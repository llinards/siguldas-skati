<div class="border-ss-gray text-ss-gray rounded-3xl border-1 p-6 shadow-md lg:p-8">
    @if ($bookingError)
        <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $bookingError }}</div>
    @endif

    <div class="grid gap-x-10 gap-y-8 lg:grid-cols-2">
        {{-- Dates, guests & add-ons --}}
        <div class="flex flex-col space-y-6">
            <div class="flex flex-col">
                <label class="mb-2">{{ __('Reģistrēšanās') }} – {{ __('Izrakstīšanās') }}</label>
                <x-input-error :messages="$errors->get('checkIn')" />
                <x-input-error :messages="$errors->get('checkOut')" />
                <div wire:ignore
                    x-data="bookingCalendar({
                        minDate: @js(now()->toDateString()),
                        disabled: @js($unavailableDates),
                        selected: @js(array_values(array_filter([$checkIn, $checkOut]))),
                        basePrice: @js((int) $product->base_price),
                        priceOverrides: @js($priceOverrides),
                    })">
                    <input x-ref="input" type="text" readonly
                        placeholder="{{ __('Izvēlies datumus') }}"
                        class="border-b w-full cursor-pointer bg-transparent" />
                </div>
            </div>

            <div class="flex flex-col">
                <label class="mb-2">{{ __('Viesi') }}</label>
                @php($maxAdults = $this->maxAdults())
                @php($maxChildren = $this->maxChildren())
                <div class="border-ss-gray rounded-2xl border-1 px-4 py-1">
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-ss-dark font-medium">{{ __('Pieaugušie') }}</p>
                            <p class="text-xs">{{ __('Maks.') }} {{ $product->person_count }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" wire:click="decrementAdults" @disabled($adults <= 1)
                                class="border-ss-gray text-ss-dark flex h-8 w-8 items-center justify-center rounded-full border-1 text-lg leading-none transition hover:border-ss-dark disabled:opacity-40">−</button>
                            <span class="text-ss-dark w-5 text-center tabular-nums">{{ $adults }}</span>
                            <button type="button" wire:click="incrementAdults" @disabled($adults >= $maxAdults)
                                class="border-ss-gray text-ss-dark flex h-8 w-8 items-center justify-center rounded-full border-1 text-lg leading-none transition hover:border-ss-dark disabled:opacity-40">+</button>
                        </div>
                    </div>

                    <div class="border-ss-gray/30 flex items-center justify-between border-t py-3">
                        <div>
                            <p class="text-ss-dark font-medium">{{ __('Bērni') }}</p>
                            @if ($product->children_count > 0)
                                <p class="text-xs">{{ __('Maks.') }} {{ $product->children_count }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" wire:click="decrementChildren" @disabled($children <= 0)
                                class="border-ss-gray text-ss-dark flex h-8 w-8 items-center justify-center rounded-full border-1 text-lg leading-none transition hover:border-ss-dark disabled:opacity-40">−</button>
                            <span class="text-ss-dark w-5 text-center tabular-nums">{{ $children }}</span>
                            <button type="button" wire:click="incrementChildren" @disabled($children >= $maxChildren)
                                class="border-ss-gray text-ss-dark flex h-8 w-8 items-center justify-center rounded-full border-1 text-lg leading-none transition hover:border-ss-dark disabled:opacity-40">+</button>
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-xs">{{ __('Kopā līdz :count viesiem.', ['count' => $this->totalCapacity()]) }}</p>
                @if ($guestError)
                    <p class="mt-1 text-xs text-red-600">{{ $guestError }}</p>
                @endif
            </div>

            @if ($addons->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($addons as $addon)
                        <label class="flex cursor-pointer items-start gap-3 text-sm">
                            <span class="relative shrink-0">
                                <input
                                    type="checkbox"
                                    wire:model="selectedAddons.{{ $addon->id }}"
                                    class="peer border-ss-dark bg-ss checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 transition duration-200"
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
                            <span>{{ $addon->getTranslation('description', app()->getLocale()) ?: $addon->getTranslation('name', app()->getLocale()) }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Guest details, price & reserve --}}
        <div class="flex flex-col space-y-6">
            <div class="flex flex-col">
                <label class="mb-2" for="guestName">@lang('Vārds, uzvārds') *</label>
                <x-input-error :messages="$errors->get('guestName')" />
                <input class="border-b bg-transparent" id="guestName" wire:model="guestName" type="text" />
            </div>
            <div class="flex flex-col">
                <label class="mb-2" for="guestEmail">@lang('E-pasts') *</label>
                <x-input-error :messages="$errors->get('guestEmail')" />
                <input class="border-b bg-transparent" id="guestEmail" wire:model="guestEmail" type="email" />
            </div>
            <div class="flex flex-col">
                <label class="mb-2" for="guestPhone">@lang('Tālrunis') *</label>
                <x-input-error :messages="$errors->get('guestPhone')" />
                <input class="border-b bg-transparent" id="guestPhone" wire:model="guestPhone" type="text" />
            </div>

            @if ($quote)
                <div class="mt-auto space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>€{{ number_format(($quote->nightsTotal / max($quote->nights, 1)) / 100, 0) }} × {{ $quote->nights }} {{ __('naktis') }}</span>
                        <span>€{{ number_format($quote->nightsTotal / 100, 2) }}</span>
                    </div>
                    <div class="border-ss-gray/30 text-ss-dark flex justify-between border-t pt-2 font-medium">
                        <span>{{ __('Kopā') }}</span><span>€{{ number_format($quote->grandTotal / 100, 2) }}</span>
                    </div>
                </div>
            @endif

            <x-btn-primary
                type="button"
                class="w-full"
                wire:click="reserve"
                wire:loading.attr="disabled"
                wire:target="reserve"
            >
                <span wire:loading.remove wire:target="reserve">@lang('Rezervēt brīvdienu māju')</span>
                <span wire:loading wire:target="reserve" class="flex items-center justify-center">
                    <svg class="mr-3 -ml-1 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </x-btn-primary>
        </div>
    </div>
</div>
