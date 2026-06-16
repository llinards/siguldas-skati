<div class="max-w-2xl space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $booking->reference }}</h1>
        <p class="text-neutral-600">{{ __(ucfirst($booking->status->value)) }}</p>
    </div>

    <dl class="grid grid-cols-2 gap-3 text-sm">
        <dt class="font-medium">{{ __('Viesis') }}</dt>
        <dd>{{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}</dd>
        <dt class="font-medium">{{ __('Datumi') }}</dt>
        <dd>{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</dd>
        <dt class="font-medium">{{ __('Viesi') }}</dt>
        <dd>{{ $booking->adults }} + {{ $booking->children }}</dd>
        <dt class="font-medium">{{ __('Kopā') }}</dt>
        <dd>{{ $booking->formattedTotal() }}</dd>
        @if ($booking->refund_amount)
            <dt class="font-medium">{{ __('Atmaksātā summa') }}</dt>
            <dd>{{ $booking->formattedRefund() }}</dd>
        @endif
    </dl>

    @if ($booking->addons->isNotEmpty())
        <div>
            <h2 class="mb-2 font-medium">{{ __('Pieprasītie papildinājumi') }}</h2>
            <ul class="list-disc pl-5 text-sm">
                @foreach ($booking->addons as $addon)
                    <li>{{ $addon->pivot->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <h2 class="mb-2 font-medium">{{ __('Piezīmes') }}</h2>
        <textarea wire:model="notes" rows="3" class="w-full rounded-lg border p-2"></textarea>
        <button type="button" wire:click="saveNotes" class="mt-2 rounded-full bg-neutral-800 px-5 py-2 text-white">{{ __('Saglabāt piezīmes') }}</button>
    </div>

    @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <div class="rounded-lg border border-red-200 p-4">
            <h2 class="mb-2 font-medium text-red-700">{{ __('Atmaksa') }}</h2>
            <input type="text" wire:model="refundReason" placeholder="{{ __('Iemesls') }}" class="mb-2 w-full rounded-lg border p-2" />
            <button type="button" wire:click="refund" wire:confirm="{{ __('Vai tiešām veikt atmaksu?') }}"
                class="rounded-full bg-red-600 px-5 py-2 text-white">
                {{ __('Veikt pilnu atmaksu') }}
            </button>
        </div>
    @endif
</div>
