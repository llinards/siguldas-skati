<div class="space-y-6">
    <div>
        <p class="text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
        <p class="text-neutral-600">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</p>
        <p class="text-neutral-600">{{ __('Kopā') }}: {{ $booking->formattedTotal() }}</p>
        <p class="text-neutral-600">{{ __('Statuss') }}: {{ __(ucfirst($booking->status->value)) }}</p>
    </div>

    @if ($message)
        <p class="rounded-lg bg-neutral-100 p-4 text-sm text-neutral-700">{{ $message }}</p>
    @endif

    @if ($booking->status === \App\Enums\BookingStatus::Cancelled)
        <p class="text-neutral-600">{{ __('Rezervācija ir atcelta.') }}
            @if ($booking->refund_amount){{ __('Atmaksātā summa') }}: {{ $booking->formattedRefund() }}@endif
        </p>
    @elseif ($booking->isRefundableByGuest())
        <button type="button" wire:click="requestRefund" wire:loading.attr="disabled"
            class="rounded-full bg-ss-dark px-6 py-3 text-white">
            {{ __('Atcelt rezervāciju un saņemt atmaksu') }}
        </button>
    @else
        <p class="text-sm text-neutral-500">
            {{ __('Bezmaksas atcelšana iespējama līdz 7 dienām pirms ierašanās. Lūdzu, sazinieties ar mums.') }}
        </p>
    @endif
</div>
