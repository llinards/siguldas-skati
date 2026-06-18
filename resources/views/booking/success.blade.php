<x-app-layout>
    <div class="bg-ss flex min-h-[70vh] flex-col items-center justify-center px-4 py-24 text-center">
        <div class="mx-auto flex max-w-2xl flex-col items-center">
        <x-booking.status-icon :status="$booking->status" class="mb-8" />

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Paldies – Jūsu rezervācija ir apstiprināta!') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray">{{ __('Jūsu rezervācijas numurs') }}: {{ $booking->reference }}</p>

            <x-btn-primary href="{{ route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token]) }}"
                class="mt-8">
                {{ __('Pārvaldīt rezervāciju') }}
            </x-btn-primary>
        @else
            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Apstiprinām jūsu maksājumu') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray">{{ __('Jūsu rezervācijas numurs') }}: {{ $booking->reference }}</p>

            <x-need-help class="mt-4 max-w-md" />
        @endif
        </div>
    </div>
</x-app-layout>
