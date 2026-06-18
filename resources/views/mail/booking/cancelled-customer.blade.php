<x-mail.layout :title="__('Rezervācija atcelta')" :subtitle="$booking->reference">
    <div class="greeting">{{ __('Rezervācija ir atcelta.') }}</div>

    <div class="form-section">
        <h3>{{ __('Rezervācijas informācija') }}</h3>

        <div class="field">
            <div class="field-label">{{ __('Rezervācijas numurs') }}</div>
            <div class="field-value">{{ $booking->reference }}</div>
        </div>
        @if ($refunded)
            <div class="field">
                <div class="field-label">{{ __('Atmaksātā summa') }}</div>
                <div class="field-value">{{ $booking->formattedRefund() }}</div>
            </div>
        @endif
    </div>

    @if ($refunded)
        <div class="info-box">
            {{ __('Atmaksa parasti tiek saņemta 5–10 darba dienu laikā.') }}
        </div>
    @endif

    <x-slot:footer>
        <p>{{ __('Ar cieņu') }},<br />{{ config('app.name') }}</p>
    </x-slot:footer>
</x-mail.layout>
