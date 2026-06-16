<div class="p-6">
    <h1 class="mb-6 text-2xl font-semibold">{{ __('Rezervācijas') }}</h1>

    <div class="overflow-x-auto rounded-lg border">
        <table class="min-w-full divide-y text-sm">
            <thead class="bg-neutral-50 text-left">
                <tr>
                    <th class="px-4 py-3">{{ __('Numurs') }}</th>
                    <th class="px-4 py-3">{{ __('Viesis') }}</th>
                    <th class="px-4 py-3">{{ __('Datumi') }}</th>
                    <th class="px-4 py-3">{{ __('Kopā') }}</th>
                    <th class="px-4 py-3">{{ __('Statuss') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $booking->reference }}</td>
                        <td class="px-4 py-3">{{ $booking->guest_name }}</td>
                        <td class="px-4 py-3">{{ $booking->check_in->format('d.m.Y') }} – {{ $booking->check_out->format('d.m.Y') }}</td>
                        <td class="px-4 py-3">{{ $booking->formattedTotal() }}</td>
                        <td class="px-4 py-3">{{ __(ucfirst($booking->status->value)) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.booking.detail', $booking->id) }}" class="text-blue-600 underline">{{ __('Apskatīt') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-neutral-500">{{ __('Nav rezervāciju.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
