<x-mail::message>
# {{ __('Jauna rezervācija') }} — {{ $booking->reference }}

- {{ __('Māja') }}: {{ $booking->product?->getTranslation('title', app()->getLocale()) }}
- {{ __('Reģistrēšanās') }}: {{ $booking->check_in->format('d.m.Y') }}
- {{ __('Izrakstīšanās') }}: {{ $booking->check_out->format('d.m.Y') }}
- {{ __('Viesi') }}: {{ $booking->adults }} + {{ $booking->children }}
- {{ __('Kopā') }}: {{ $booking->formattedTotal() }}

{{ __('Viesis') }}: {{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}

@if ($booking->addons->isNotEmpty())
{{ __('Pieprasītie papildinājumi') }}:
@foreach ($booking->addons as $addon)
- {{ $addon->pivot->name }}
@endforeach
@endif
</x-mail::message>
