<x-app-layout :title="__('Maksājums atcelts')">
    <div class="bg-ss flex min-h-[70vh] flex-col items-center justify-center px-4 py-24 text-center">
        <div class="mx-auto flex max-w-2xl flex-col items-center">
            {{-- Payment not completed: the same X mark as the success/manage error state. --}}
            <svg class="mb-8 h-14 w-14 text-[#2f3a1f]" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8" />
            </svg>

            <h1 class="text-3xl text-[#2f3a1f] sm:text-4xl">{{ __('Maksājums atcelts') }}</h1>

            <hr class="my-8 w-40 border-t border-[#2f3a1f]/30" />

            <p class="text-ss-gray max-w-md">{{ __('Jūsu rezervācija netika pabeigta. Datumi atkal būs pieejami pēc neilga brīža.') }}</p>

            <x-need-help class="mt-4 max-w-md" />

            <x-btn-primary href="{{ route('home') }}" class="mt-8">
                {{ __('Atpakaļ uz sākumu') }}
            </x-btn-primary>
        </div>
    </div>
</x-app-layout>
