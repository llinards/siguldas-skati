<x-mail::message>
# {{ __('Rezervācija atcelta') }} — {{ $booking->reference }}

- {{ __('Viesis') }}: {{ $booking->guest_name }} · {{ $booking->guest_email }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
@if ($refunded)
- {{ __('Atmaksātā summa') }}: {{ $booking->formattedRefund() }}
@endif
</x-mail::message>
