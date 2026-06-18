<div>
    <x-admin.flash-message />

    <div class="space-y-12">
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ $booking->reference }}</h2>
            <p class="text-sm/6 text-gray-600">
                {{ __('Statuss') }}: <span class="font-medium text-gray-900">{{ __($booking->status->label()) }}</span>
            </p>

            <dl class="mt-5 grid grid-cols-1 gap-5 text-sm sm:grid-cols-2">
                <div>
                    <dt class="font-medium text-gray-900">{{ __('Viesis') }}</dt>
                    <dd class="mt-1 text-gray-600">{{ $booking->guest_name }} · {{ $booking->guest_email }} · {{ $booking->guest_phone }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">{{ __('Datumi') }}</dt>
                    <dd class="mt-1 text-gray-600">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">{{ __('Viesi') }}</dt>
                    <dd class="mt-1 text-gray-600">{{ $booking->adults }} + {{ $booking->children }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">{{ __('Kopā') }}</dt>
                    <dd class="mt-1 text-gray-600">{{ $booking->formattedTotal() }}</dd>
                </div>
                @if ($booking->refund_amount)
                    <div>
                        <dt class="font-medium text-gray-900">{{ __('Atmaksātā summa') }}</dt>
                        <dd class="mt-1 text-gray-600">{{ $booking->formattedRefund() }}</dd>
                    </div>
                @endif
            </dl>

            @if ($booking->addons->isNotEmpty())
                <div class="mt-5">
                    <dt class="text-sm font-medium text-gray-900">{{ __('Pieprasītie papildinājumi') }}</dt>
                    <ul class="mt-1 list-disc pl-5 text-sm text-gray-600">
                        @foreach ($booking->addons as $addon)
                            <li>{{ $addon->pivot->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Maksājuma statuss') }}</h2>
            <label for="paymentStatus" class="block text-sm/6 font-medium text-gray-900">{{ __('Statuss') }}</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center">
                <select
                    id="paymentStatus"
                    wire:model="paymentStatus"
                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:w-64 sm:text-sm/6"
                >
                    @foreach ([\App\Enums\BookingStatus::Confirmed, \App\Enums\BookingStatus::Cancelled] as $status)
                        <option value="{{ $status->value }}" @selected($paymentStatus === $status->value)>
                            {{ __($status->label()) }}
                        </option>
                    @endforeach
                </select>
                <x-btn-primary type="button" wire:click="changeStatus" wire:confirm="{{ __('Vai tiešām mainīt maksājuma statusu?') }}">
                    @lang('Saglabāt statusu')
                </x-btn-primary>
            </div>
            <p class="mt-2 text-sm text-gray-500">{{ __('Statusa maiņa neveic atmaksu un nesūta e-pastus.') }}</p>
            <x-input-error :messages="$errors->get('paymentStatus')" class="mt-2" />
        </div>

        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Piezīmes') }}</h2>
            <div class="mt-2">
                <textarea
                    wire:model="notes"
                    rows="3"
                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                ></textarea>
            </div>
            <div class="mt-4 flex items-center justify-end">
                <x-btn-primary type="button" wire:click="saveNotes">
                    @lang('Saglabāt piezīmes')
                </x-btn-primary>
            </div>
        </div>

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div>
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none text-red-700">{{ __('Atmaksa') }}</h2>
                <label for="refundReason" class="block text-sm/6 font-medium text-gray-900">{{ __('Iemesls') }}</label>
                <div class="mt-2">
                    <input
                        id="refundReason"
                        type="text"
                        wire:model="refundReason"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-red-600 sm:text-sm/6"
                    />
                </div>
                <div class="mt-4 flex items-center justify-end">
                    <x-btn-danger wire:click="refund" wire:confirm="{{ __('Vai tiešām veikt atmaksu?') }}">
                        @lang('Veikt pilnu atmaksu')
                    </x-btn-danger>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-6 flex items-center justify-end gap-x-6">
        <a href="{{ route('dashboard.bookings') }}" class="text-sm/6 font-semibold text-gray-900">
            @lang('Atpakaļ')
        </a>
    </div>
</div>
