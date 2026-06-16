<x-mail::message>
# {{ __('Paldies! Rezervācija apstiprināta.') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

@if ($booking->addons->isNotEmpty())
{{ __('Pieprasītie papildinājumi (sazināsimies par detaļām)') }}:
@foreach ($booking->addons as $addon)
- {{ $addon->pivot->name }}
@endforeach
@endif

<x-mail::button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
{{ __('Apskatīt rezervāciju') }}
</x-mail::button>

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
