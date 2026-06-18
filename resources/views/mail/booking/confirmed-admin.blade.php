<x-mail.layout :title="__('Jauna rezervācija')" :subtitle="$booking->reference">
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

    <div class="form-section">
        <h3>{{ __('Viesis') }}</h3>

        <div class="field">
            <div class="field-label">{{ __('Vārds, uzvārds') }}</div>
            <div class="field-value">{{ $booking->guest_name }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('E-pasts') }}</div>
            <div class="field-value">{{ $booking->guest_email }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Tālrunis') }}</div>
            <div class="field-value">{{ $booking->guest_phone }}</div>
        </div>
    </div>

    @if ($booking->wants_sauna_jacuzzi || $booking->wants_baby_cot)
        <div class="form-section">
            <h3>{{ __('Pieprasītie papildinājumi') }}</h3>
            @if ($booking->wants_sauna_jacuzzi)
                <div class="field"><div class="field-value">{{ __('Sauna un džakuzi') }}</div></div>
            @endif
            @if ($booking->wants_baby_cot)
                <div class="field"><div class="field-value">{{ __('Bērnu gultiņa') }}</div></div>
            @endif
        </div>
    @endif

    <div class="action-buttons">
        <x-mail.button :url="route('dashboard.booking.detail', $booking->id)">
            {{ __('Apskatīt rezervāciju') }}
        </x-mail.button>
    </div>
</x-mail.layout>
