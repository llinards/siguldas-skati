<x-app-layout>
    <div class="bg-ss flex min-h-[70vh] flex-col items-center justify-center px-4 py-24 text-center">
        <div class="mx-auto flex w-full max-w-2xl flex-col items-center">
            {{-- Same checkmark as the success page / booking-widget add-on checkboxes. --}}
            <svg class="mb-8 h-14 w-14 text-[#2f3a1f]" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
            </svg>

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Jūsu rezervācija') }}</h1>
            @livewire('booking.manage-booking', ['booking' => $booking, 'token' => $token])
        </div>
    </div>
</x-app-layout>
