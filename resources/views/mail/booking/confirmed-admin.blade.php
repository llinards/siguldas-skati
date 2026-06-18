<x-mail::message>
# {{ __('Jauna rezervācija') }} — {{ $booking->reference }}

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

{{ __('Viesis') }}: {{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}

@if ($booking->wants_sauna_jacuzzi || $booking->wants_baby_cot)
{{ __('Pieprasītie papildinājumi') }}:
@if ($booking->wants_sauna_jacuzzi)
- {{ __('Sauna un džakuzi') }}
@endif
@if ($booking->wants_baby_cot)
- {{ __('Bērnu gultiņa') }}
@endif
@endif
</x-mail::message>
