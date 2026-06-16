<x-app-layout>
    <div class="bg-ss flex min-h-[70vh] flex-col items-center justify-center px-4 py-24 text-center">
        <div class="mx-auto flex max-w-2xl flex-col items-center">
        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            {{-- Same checkmark as the add-on checkboxes in the booking widget. --}}
            <svg class="mb-8 h-14 w-14 text-[#2f3a1f]" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
            </svg>

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Paldies – Jūsu rezervācija ir apstiprināta!') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray">{{ __('Jūsu rezervācijas numurs') }}: {{ $booking->reference }}</p>
        @else
            {{-- Payment not confirmed yet / error: the same icon family, an X mark. --}}
            <svg class="mb-8 h-14 w-14 text-[#2f3a1f]" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8" />
            </svg>

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Apstiprinām jūsu maksājumu') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray">{{ __('Jūsu rezervācijas numurs') }}: {{ $booking->reference }}</p>
        @endif
        </div>
    </div>
</x-app-layout>
