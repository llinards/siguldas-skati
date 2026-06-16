<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20">
        <h1 class="mb-8 text-2xl font-semibold text-[#2f3a1f]">{{ __('Jūsu rezervācija') }}</h1>
        @livewire('booking.manage-booking', ['booking' => $booking, 'token' => $token])
    </div>
</x-app-layout>
