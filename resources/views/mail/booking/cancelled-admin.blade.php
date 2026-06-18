<x-mail.layout :title="__('Rezervācija atcelta')" :subtitle="$booking->reference">
    <div class="form-section">
        <h3>{{ __('Rezervācijas informācija') }}</h3>

        <div class="field">
            <div class="field-label">{{ __('Viesis') }}</div>
            <div class="field-value">{{ $booking->guest_name }} · {{ $booking->guest_email }}</div>
        </div>
        <div class="field">
            <div class="field-label">{{ __('Reģistrēšanās') }}</div>
            <div class="field-value">{{ $booking->check_in->format('d.m.Y') }}</div>
        </div>
        @if ($refunded)
            <div class="field">
                <div class="field-label">{{ __('Atmaksātā summa') }}</div>
                <div class="field-value">{{ $booking->formattedRefund() }}</div>
            </div>
        @endif
    </div>

    <div class="action-buttons">
        <x-mail.button :url="route('dashboard.booking.detail', $booking->id)">
            {{ __('Apskatīt rezervāciju') }}
        </x-mail.button>
    </div>
</x-mail.layout>
