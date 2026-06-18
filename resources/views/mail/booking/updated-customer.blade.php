<x-mail::message>
# {{ __('Jūsu rezervācija ir mainīta') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

<x-mail::button :url="route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token])">
{{ __('Apskatīt rezervāciju') }}
</x-mail::button>

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
