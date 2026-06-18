<div class="mx-auto max-w-3xl">
    <x-admin.flash-message />

    @php
        $statusStyles = [
            'confirmed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-amber-100 text-amber-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'expired' => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">{{ $booking->reference }}</h1>
        <span class="{{ $statusStyles[$booking->status->value] ?? 'bg-gray-100 text-gray-600' }} inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium">
            {{ __($booking->status->label()) }}
        </span>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Viesis') }}</dt>
            <dd class="mt-0.5 text-gray-900">{{ $booking->guest_name }}</dd>
            <dd class="text-gray-600">{{ $booking->guest_email }} · {{ $booking->guest_phone }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Datumi') }}</dt>
            <dd class="mt-0.5 text-gray-900">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Viesi') }}</dt>
            <dd class="mt-0.5 text-gray-900">{{ __('Pieaugušie') }}: {{ $booking->adults }} · {{ __('Bērni') }}: {{ $booking->children }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Kopā') }}</dt>
            <dd class="mt-0.5 text-gray-900">{{ $booking->formattedTotal() }}</dd>
        </div>
        @if ($booking->refund_amount)
            <div>
                <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Atmaksātā summa') }}</dt>
                <dd class="mt-0.5 text-gray-900">{{ $booking->formattedRefund() }}</dd>
            </div>
        @endif
        @if ($booking->cancellation_reason)
            <div>
                <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Atcelšanas iemesls') }}</dt>
                <dd class="mt-0.5 text-gray-900">{{ $booking->cancellation_reason }}</dd>
            </div>
        @endif
        @if ($booking->addons->isNotEmpty())
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium tracking-wide text-gray-500 uppercase">{{ __('Pieprasītie papildinājumi') }}</dt>
                <dd class="mt-0.5 text-gray-900">{{ $booking->addons->pluck('pivot.name')->implode(', ') }}</dd>
            </div>
        @endif
    </dl>

    @if ($booking->status === \App\Enums\BookingStatus::Pending)
        <div class="mt-8 border-t border-gray-200 pt-6">
            <h2 class="text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ __('Apstiprināt rezervāciju') }}</h2>
            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-md text-sm text-gray-600">
                    {{ __('Ja maksājums ir saņemts, bet rezervācija palikusi gaidīšanas statusā, apstiprini to manuāli. Klients saņems apstiprinājuma e-pastu.') }}
                </p>
                <x-btn-primary type="button" wire:click="confirmBooking" wire:confirm="{{ __('Vai tiešām apstiprināt šo rezervāciju?') }}">
                    @lang('Apstiprināt rezervāciju')
                </x-btn-primary>
            </div>
        </div>
    @endif

    <div class="mt-8 border-t border-gray-200 pt-6">
        <h2 class="text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ __('Piezīmes') }}</h2>
        <textarea
            wire:model="notes"
            rows="3"
            class="mt-3 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
        ></textarea>
        <div class="mt-3 flex justify-end">
            <x-btn-primary type="button" wire:click="saveNotes">
                @lang('Saglabāt piezīmes')
            </x-btn-primary>
        </div>
    </div>

    @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <div class="mt-8 border-t border-gray-200 pt-6">
            <h2 class="text-xs font-semibold tracking-wide text-red-600 uppercase">{{ __('Atmaksa') }}</h2>
            <label for="refundReason" class="mt-3 block text-sm font-medium text-gray-900">{{ __('Iemesls') }}</label>
            <input
                id="refundReason"
                type="text"
                wire:model="refundReason"
                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-sm text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-red-600"
            />
            <div class="mt-3 flex justify-end">
                <x-btn-danger wire:click="refund" wire:confirm="{{ __('Vai tiešām veikt atmaksu?') }}">
                    @lang('Veikt pilnu atmaksu')
                </x-btn-danger>
            </div>
        </div>
    @endif

    <div class="mt-8 flex justify-end">
        <a href="{{ route('dashboard.bookings') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">
            @lang('Atpakaļ')
        </a>
    </div>
</div>
