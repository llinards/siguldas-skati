<x-mail.layout :title="__('Rezervācija apstiprināta')" :subtitle="$booking->reference">
    <div class="greeting">{{ __('Paldies! Rezervācija apstiprināta.') }}</div>

    <div class="form-section">
        <h3>{{ __('Rezervācijas informācija') }}</h3>

        <div class="field">
            <div class="field-label">{{ __('Māja') }}</div>
            <div class="field-value">{{ $booking->product?->getTranslation('title', app()->getLocale()) }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Reģistrēšanās') }}</div>
            <div class="field-value">{{ $booking->check_in->format('d.m.Y') }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Izrakstīšanās') }}</div>
            <div class="field-value">{{ $booking->check_out->format('d.m.Y') }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Viesi') }}</div>
            <div class="field-value">{{ $booking->adults }} + {{ $booking->children }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Kopā') }}</div>
            <div class="field-value">{{ $booking->formattedTotal() }}</div>
        </div>
    </div>

    @if ($booking->wants_sauna_jacuzzi || $booking->wants_baby_cot)
        <div class="form-section">
            <h3>{{ __('Pieprasītie papildinājumi (sazināsimies par detaļām)') }}</h3>
            @if ($booking->wants_sauna_jacuzzi)
                <div class="field"><div class="field-value">{{ __('Sauna un džakuzi') }}</div></div>
            @endif
            @if ($booking->wants_baby_cot)
                <div class="field"><div class="field-value">{{ __('Bērnu gultiņa') }}</div></div>
            @endif
        </div>
    @endif

    @if ($booking->isRefundableByGuest())
        <div class="info-box">
            {{ __('Bezmaksas atcelšana ar pilnu atmaksu iespējama līdz') }}
            <strong>{{ $booking->freeCancellationUntil()->format('d.m.Y') }}</strong>.
            {{ __('Atcelt vari, atverot rezervāciju zemāk.') }}
        </div>
    @endif

    <div class="action-buttons">
        <x-mail.button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
            {{ __('Apskatīt rezervāciju') }}
        </x-mail.button>
    </div>

    <x-slot:footer>
        <p>{{ __('Ar cieņu') }},<br />{{ config('app.name') }}</p>
    </x-slot:footer>
</x-mail.layout>
