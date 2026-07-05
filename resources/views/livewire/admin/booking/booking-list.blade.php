<div>
    <x-admin.flash-message />
    <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Rezervācijas') }}</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Numurs') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Viesis') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Māja') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Datumi') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Kopā') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('Statuss') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        <span class="sr-only">{{ __('Darbības') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                            {{ $booking->reference }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $booking->guest_name }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $booking->product->title }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $booking->formattedTotal() }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap text-gray-900">
                            {{ __($booking->status->label()) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <div class="flex items-center gap-x-1">
                                <a
                                    href="{{ route('dashboard.booking.detail', $booking->id) }}"
                                    class="inline-flex items-center rounded-md p-2 text-gray-600 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-900"
                                    title="{{ __('Apskatīt') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button
                                    wire:click="delete({{ $booking->id }})"
                                    wire:confirm="{{ __('Vai tiešām dzēst šo rezervāciju? Dati tiks neatgriezeniski izdzēsti.') }}"
                                    class="inline-flex items-center rounded-md p-2 text-red-600 transition-colors duration-200 hover:bg-red-50 hover:text-red-900"
                                    title="{{ __('Dzēst') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <p class="text-base leading-7.5 md:text-xl xl:text-2xl">
                                @lang('Nav rezervāciju.')
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</div>
