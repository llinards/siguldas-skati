<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20 text-center">
        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('Paldies! Rezervācija apstiprināta.') }}</h1>
            <p class="mt-4 text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
            <p class="mt-2 text-neutral-600">{{ $booking->check_in->toDateString() }} – {{ $booking->check_out->toDateString() }}</p>
        @else
            <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('We are confirming your payment') }}</h1>
            <p class="mt-4 text-neutral-600">{{ __('Rezervācijas numurs') }}: <strong>{{ $booking->reference }}</strong></p>
        @endif
    </div>
</x-app-layout>
