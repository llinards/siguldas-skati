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

            <x-btn-primary href="{{ route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token]) }}"
                class="mt-8">
                {{ __('Pārvaldīt rezervāciju') }}
            </x-btn-primary>
        @else
            {{-- Payment still being confirmed: an hourglass to signal "please wait". --}}
            <svg class="mb-8 h-14 w-14 text-[#2f3a1f]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14M5 21h14M7 3v3l5 6 5-6V3M7 21v-3l5-6 5 6v3" />
            </svg>

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Apstiprinām jūsu maksājumu') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray">{{ __('Jūsu rezervācijas numurs') }}: {{ $booking->reference }}</p>

            <x-need-help class="mt-4 max-w-md" />
        @endif
        </div>
    </div>
</x-app-layout>
