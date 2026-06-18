<x-mail.layout :title="__('Rezervācija ir mainīta')" :subtitle="$booking->reference">
    <div class="greeting">{{ __('Jūsu rezervācija ir atjaunināta.') }}</div>

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
            <div class="field-label">{{ __('Kopā') }}</div>
            <div class="field-value">{{ $booking->formattedTotal() }}</div>
        </div>
    </div>

    <div class="action-buttons">
        <x-mail.button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
            {{ __('Apskatīt rezervāciju') }}
        </x-mail.button>
    </div>

    <x-slot:footer>
        <p>{{ __('Ar cieņu') }},<br />{{ config('app.name') }}</p>
    </x-slot:footer>
</x-mail.layout>
