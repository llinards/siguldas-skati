<x-mail::message>
# {{ __('Rezervācija atcelta') }}

{{ __('Rezervācijas numurs') }}: **{{ $booking->reference }}**

@if ($refunded)
{{ __('Atmaksātā summa') }}: **{{ $booking->formattedRefund() }}**

{{ __('Atmaksa parasti tiek saņemta 5–10 darba dienu laikā.') }}
@else
{{ __('Rezervācija ir atcelta.') }}
@endif

{{ __('Ar cieņu') }},<br>
{{ config('app.name') }}
</x-mail::message>
