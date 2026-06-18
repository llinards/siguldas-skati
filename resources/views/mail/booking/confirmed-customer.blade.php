<x-mail::message>
# {{ __('Paldies! Rezervācija apstiprināta.') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

@if ($booking->wants_sauna_jacuzzi || $booking->wants_baby_cot)
{{ __('Pieprasītie papildinājumi (sazināsimies par detaļām)') }}:
@if ($booking->wants_sauna_jacuzzi)
- {{ __('Sauna un džakuzi') }}
@endif
@if ($booking->wants_baby_cot)
- {{ __('Bērnu gultiņa') }}
@endif
@endif

@if ($booking->isRefundableByGuest())
{{ __('Bezmaksas atcelšana ar pilnu atmaksu iespējama līdz') }} **{{ $booking->freeCancellationUntil()->format('d.m.Y') }}**. {{ __('Atcelt vari, atverot rezervāciju zemāk.') }}
@endif

<x-mail::button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
{{ __('Apskatīt rezervāciju') }}
</x-mail::button>

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
