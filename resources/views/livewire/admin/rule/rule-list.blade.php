@php use App\Models\Rule; @endphp
<div>
    <x-admin.flash-message/>
    <h2 class="text-h-sm-mob lg:text-h-mob mb-6 leading-none">{{ __('Noteikumi') }}</h2>

    <!-- House Rules Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
            {{ __('Mājokļa noteikumi') }}
        </h3>
        <div wire:sortable="updateRuleOrder" class="space-y-4" data-section="house">
            @forelse ($rules->where('section', Rule::SECTION_HOUSE) as $rule)
                <div
                    class="flex items-center overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                    wire:key="rule-{{ $rule->id }}"
                    wire:sortable.item="{{ $rule->id }}"
                >
                    <div class="flex items-center justify-center p-4 cursor-move text-gray-400 hover:text-gray-600"
                         wire:sortable.handle>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                        </svg>
                    </div>

                    <div class="flex items-center justify-center bg-gray-50 p-6 flex-shrink-0">
                        @if ($rule->icon_image)
                            <img
                                src="{{ Storage::url($rule->icon_image) }}"
                                alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                class="h-8 w-8 object-contain"
                            />
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                                <span class="text-xs text-gray-500">{{ __('Nav') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 p-4">
                        <h4 class="mb-1 text-lg font-semibold text-gray-900" title="{{ $rule->title }}">
                            {{ $rule->getTranslation('title', app()->getLocale()) }}
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ __('Mājokļa noteikumi') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 p-4 flex-shrink-0">
                        <button
                            wire:click="toggleActive({{ $rule->id }})"
                            class="{{ $rule->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} inline-flex items-center rounded-md px-3 py-1 text-sm font-medium transition-colors duration-200"
                        >
                            {{ $rule->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <a
                            href="{{ route('dashboard.rule.edit', $rule->id) }}"
                            class="inline-flex items-center rounded-md p-2 text-indigo-600 transition-colors duration-200 hover:bg-indigo-50 hover:text-indigo-900"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                            </svg>
                        </a>

                        <button
                            wire:click="deleteRule({{ $rule->id }})"
                            wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo noteikumu?') }}"
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
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-500">
                    <p class="text-sm">{{ __('Nav pievienoti mājokļa noteikumi') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Safety Rules Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
            {{ __('Drošība un īpašums') }}
        </h3>
        <div wire:sortable="updateRuleOrder" class="space-y-4" data-section="safety">
            @forelse ($rules->where('section', Rule::SECTION_SAFETY) as $rule)
                <div
                    class="flex items-center overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                    wire:key="rule-{{ $rule->id }}"
                    wire:sortable.item="{{ $rule->id }}"
                >
                    <div class="flex items-center justify-center p-4 cursor-move text-gray-400 hover:text-gray-600"
                         wire:sortable.handle>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                        </svg>
                    </div>

                    <div class="flex items-center justify-center bg-gray-50 p-6 flex-shrink-0">
                        @if ($rule->icon_image)
                            <img
                                src="{{ Storage::url($rule->icon_image) }}"
                                alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                class="h-8 w-8 object-contain"
                            />
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                                <span class="text-xs text-gray-500">{{ __('Nav') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 p-4">
                        <h4 class="mb-1 text-lg font-semibold text-gray-900" title="{{ $rule->title }}">
                            {{ $rule->getTranslation('title', app()->getLocale()) }}
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ __('Drošība un īpašums') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 p-4 flex-shrink-0">
                        <button
                            wire:click="toggleActive({{ $rule->id }})"
                            class="{{ $rule->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} inline-flex items-center rounded-md px-3 py-1 text-sm font-medium transition-colors duration-200"
                        >
                            {{ $rule->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <a
                            href="{{ route('dashboard.rule.edit', $rule->id) }}"
                            class="inline-flex items-center rounded-md p-2 text-indigo-600 transition-colors duration-200 hover:bg-indigo-50 hover:text-indigo-900"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                            </svg>
                        </a>

                        <button
                            wire:click="deleteRule({{ $rule->id }})"
                            wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo noteikumu?') }}"
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
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-500">
                    <p class="text-sm">{{ __('Nav pievienoti drošības noteikumi') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
            {{ __('Aizliegts') }}
        </h3>
        <div wire:sortable="updateRuleOrder" class="space-y-4" data-section="safety">
            @forelse ($rules->where('section', Rule::SECTION_PROHIBITED) as $rule)
                <div
                    class="flex items-center overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                    wire:key="rule-{{ $rule->id }}"
                    wire:sortable.item="{{ $rule->id }}"
                >
                    <div class="flex items-center justify-center p-4 cursor-move text-gray-400 hover:text-gray-600"
                         wire:sortable.handle>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                        </svg>
                    </div>

                    <div class="flex items-center justify-center bg-gray-50 p-6 flex-shrink-0">
                        @if ($rule->icon_image)
                            <img
                                src="{{ Storage::url($rule->icon_image) }}"
                                alt="{{ $rule->getTranslation('title', app()->getLocale()) }}"
                                class="h-8 w-8 object-contain"
                            />
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                                <span class="text-xs text-gray-500">{{ __('Nav') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 p-4">
                        <h4 class="mb-1 text-lg font-semibold text-gray-900" title="{{ $rule->title }}">
                            {{ $rule->getTranslation('title', app()->getLocale()) }}
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ __('Drošība un īpašums') }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 p-4 flex-shrink-0">
                        <button
                            wire:click="toggleActive({{ $rule->id }})"
                            class="{{ $rule->is_active ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} inline-flex items-center rounded-md px-3 py-1 text-sm font-medium transition-colors duration-200"
                        >
                            {{ $rule->is_active ? __('Deaktivizēt') : __('Aktivizēt') }}
                        </button>

                        <a
                            href="{{ route('dashboard.rule.edit', $rule->id) }}"
                            class="inline-flex items-center rounded-md p-2 text-indigo-600 transition-colors duration-200 hover:bg-indigo-50 hover:text-indigo-900"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                            </svg>
                        </a>

                        <button
                            wire:click="deleteRule({{ $rule->id }})"
                            wire:confirm="{{ __('Vai esat pārliecināts, ka vēlaties dzēst šo noteikumu?') }}"
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
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-500">
                    <p class="text-sm">{{ __('Nav pievienoti aizliegumi') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-end">
        <x-btn-primary :href="route('dashboard.rule.add')">
            {{ __('Pievienot jaunu noteikumu') }}
        </x-btn-primary>
    </div>
</div>
