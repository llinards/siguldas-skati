<x-app-layout>
    <div class="bg-ss flex min-h-[70vh] flex-col items-center justify-center px-4 py-24 text-center">
        <div class="mx-auto flex w-full max-w-2xl flex-col items-center">
            <x-booking.status-icon :status="$booking->status" class="mb-8" />

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">
                @switch($booking->status)
                    @case(\App\Enums\BookingStatus::Confirmed)
                        {{ __('Paldies – Jūsu rezervācija ir apstiprināta!') }}
                        @break
                    @case(\App\Enums\BookingStatus::Pending)
                        {{ __('Apstiprinām jūsu maksājumu') }}
                        @break
                    @case(\App\Enums\BookingStatus::Cancelled)
                        {{ __('Rezervācija ir atcelta') }}
                        @break
                    @case(\App\Enums\BookingStatus::Expired)
                        {{ __('Rezervācijas termiņš ir beidzies') }}
                        @break
                @endswitch
            </h1>

            @livewire('booking.manage-booking', ['booking' => $booking, 'token' => $token])
        </div>
    </div>
</x-app-layout>
