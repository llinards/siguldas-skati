@props(['status'])

@switch($status)
    @case(\App\Enums\BookingStatus::Confirmed)
        {{-- Checkmark: booking confirmed. --}}
        <svg {{ $attributes->merge(['class' => 'h-14 w-14 text-[#2f3a1f]']) }} fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
        </svg>
        @break

    @case(\App\Enums\BookingStatus::Pending)
        {{-- Hourglass: payment still being confirmed. --}}
        <svg {{ $attributes->merge(['class' => 'h-14 w-14 text-[#2f3a1f]']) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14M5 21h14M7 3v3l5 6 5-6V3M7 21v-3l5-6 5 6v3" />
        </svg>
        @break

    @default
        {{-- X: cancelled or expired. --}}
        <svg {{ $attributes->merge(['class' => 'h-14 w-14 text-[#2f3a1f]']) }} fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8" />
        </svg>
@endswitch
