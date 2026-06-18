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
                    <dd class="mt-1 text-gray-600">{{ __('Pieaugušie') }}: {{ $booking->adults }} · {{ __('Bērni') }}: {{ $booking->children }}</dd>
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
                @if ($booking->cancellation_reason)
                    <div>
                        <dt class="font-medium text-gray-900">{{ __('Atcelšanas iemesls') }}</dt>
                        <dd class="mt-1 text-gray-600">{{ $booking->cancellation_reason }}</dd>
                    </div>
                @endif
            </dl>

            @if ($booking->wants_sauna_jacuzzi || $booking->wants_baby_cot)
                <div class="mt-5">
                    <dt class="text-sm font-medium text-gray-900">{{ __('Pieprasītie papildinājumi') }}</dt>
                    <ul class="mt-1 list-disc pl-5 text-sm text-gray-600">
                        @if ($booking->wants_sauna_jacuzzi)
                            <li>{{ __('Sauna un džakuzi') }}</li>
                        @endif
                        @if ($booking->wants_baby_cot)
                            <li>{{ __('Bērnu gultiņa') }}</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        @if ($booking->status === \App\Enums\BookingStatus::Pending)
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Apstiprināt rezervāciju') }}</h2>
                <p class="text-sm text-gray-600">
                    {{ __('Ja maksājums ir saņemts, bet rezervācija palikusi gaidīšanas statusā, apstiprini to manuāli. Klients saņems apstiprinājuma e-pastu.') }}
                </p>
                <div class="mt-4 flex items-center justify-end">
                    <x-btn-primary type="button" wire:click="confirmBooking" wire:confirm="{{ __('Vai tiešām apstiprināt šo rezervāciju?') }}">
                        @lang('Apstiprināt rezervāciju')
                    </x-btn-primary>
                </div>
            </div>
        @endif

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Mainīt datumus') }}</h2>
                <p class="mb-4 text-sm text-gray-600">{{ __('Cena tiks pārrēķināta. Maksājuma starpība jānokārto atsevišķi.') }}</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="newCheckIn" class="block text-sm/6 font-medium text-gray-900">{{ __('Reģistrēšanās') }}</label>
                        <div class="mt-2">
                            <input
                                id="newCheckIn"
                                type="date"
                                wire:model.live="newCheckIn"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('newCheckIn')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="newCheckOut" class="block text-sm/6 font-medium text-gray-900">{{ __('Izrakstīšanās') }}</label>
                        <div class="mt-2">
                            <input
                                id="newCheckOut"
                                type="date"
                                wire:model.live="newCheckOut"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                            />
                            <x-input-error :messages="$errors->get('newCheckOut')" class="mt-2" />
                        </div>
                    </div>
                </div>

                @php($preview = $this->datePreview)
                @if ($preview)
                    <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm">
                        <div class="flex items-center justify-between text-gray-900">
                            <span>{{ __('Naktis') }}: {{ $preview['nights'] }}</span>
                            <span class="font-medium">{{ __('Jaunā cena') }}: {{ number_format($preview['total'] / 100, 2, ',', ' ') }} €</span>
                        </div>
                        <p class="mt-1
                            @if ($preview['difference'] > 0) text-amber-700
                            @elseif ($preview['difference'] < 0) text-green-700
                            @else text-gray-500 @endif">
                            @if ($preview['difference'] > 0)
                                {{ __('Jāiekasē papildus') }}: {{ number_format($preview['difference'] / 100, 2, ',', ' ') }} €
                            @elseif ($preview['difference'] < 0)
                                {{ __('Jāatmaksā') }}: {{ number_format(-$preview['difference'] / 100, 2, ',', ' ') }} €
                            @else
                                {{ __('Cena nemainās.') }}
                            @endif
                        </p>
                        @if (! $preview['available'])
                            <p class="mt-2 font-medium text-red-700">{{ __('Šie datumi nav pieejami.') }}</p>
                        @elseif ($preview['belowMin'])
                            <p class="mt-2 font-medium text-red-700">{{ __('Minimālais nakšu skaits') }}: {{ $preview['minNights'] }}</p>
                        @endif
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-end">
                    <x-btn-primary
                        type="button"
                        :disabled="$preview && (! $preview['available'] || $preview['belowMin'])"
                        wire:click="changeDates"
                        wire:confirm="{{ __('Vai tiešām mainīt rezervācijas datumus?') }}"
                    >
                        @lang('Saglabāt datumus')
                    </x-btn-primary>
                </div>
            </div>
        @endif

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
