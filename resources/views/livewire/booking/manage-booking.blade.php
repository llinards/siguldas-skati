<div class="flex w-full flex-col items-center text-center">
    <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

    <div class="text-ss-gray space-y-1">
        <p>{{ __('Rezervācijas numurs') }}: <strong class="text-[#2f3a1f]">{{ $booking->reference }}</strong></p>
        <p>{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</p>
        <p>{{ __('Kopā') }}: {{ $booking->formattedTotal() }}</p>
        <p>{{ __('Statuss') }}: <strong class="text-[#2f3a1f]">{{ __($booking->status->label()) }}</strong></p>
    </div>

    @if ($message)
        <p class="mt-6 rounded-lg bg-white/60 px-4 py-3 text-sm text-neutral-700">{{ $message }}</p>
    @endif

    @if ($booking->status === \App\Enums\BookingStatus::Cancelled)
        <p class="text-ss-gray mt-6">{{ __('Rezervācija ir atcelta.') }}
            @if ($booking->refund_amount) {{ __('Atmaksātā summa') }}: {{ $booking->formattedRefund() }}@endif
        </p>
    @elseif ($booking->isRefundableByGuest())
        <x-btn-danger type="button"
            wire:click="requestRefund"
            wire:confirm="{{ __('Vai tiešām vēlaties atcelt šo rezervāciju? Atcelšana ir neatgriezeniska.') }}"
            wire:loading.attr="disabled"
            class="mt-8">
            {{ __('Atcelt rezervāciju un saņemt atmaksu') }}
        </x-btn-danger>
    @elseif ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <p class="text-ss-gray mt-8 max-w-md text-sm">
            {{ __('Bezmaksas atcelšana iespējama līdz 7 dienām pirms ierašanās. Lūdzu, sazinieties ar mums.') }}
        </p>
    @endif

    <x-need-help class="mt-6 max-w-md" />
</div>
