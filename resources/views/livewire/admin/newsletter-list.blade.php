<div>
    <x-admin.flash-message />
    <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none">{{ __('Pieteikumi jaunumiem') }}</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                        {{ __('E-pasts') }}
                    </th>
                    <th
                        colspan="2"
                        class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                    >
                        {{ __('Pieteikšanās datums') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($subscribers as $subscriber)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                            {{ $subscriber->email }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                            {{ $subscriber->created_at }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <button
                                wire:click="destroy({{ $subscriber->id }})"
                                wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo pieteikumu?') }}"
                                class="inline-flex items-center rounded-md p-2 text-red-600 transition-colors duration-200 hover:bg-red-50 hover:text-red-900"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    ></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p class="text-base leading-7.5 md:text-xl xl:text-2xl">
                            @lang('Šobrīd nav pieteikumu jaunumiem!')
                        </p>
                    </div>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6 flex items-center justify-end gap-x-6">
        <a href="{{ route('dashboard') }}" class="text-sm/6 font-semibold text-gray-900">
            @lang('Atcelt')
        </a>
        <x-btn-danger
            wire:click="destroyAll"
            wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst visus pieteikumus?') }}"
        >
            @lang('Dzēst visus pieteikumus')
        </x-btn-danger>
    </div>
</div>
