<x-app-layout>
    <div class="mx-auto max-w-xl px-4 py-20 text-center">
        <h1 class="text-2xl font-semibold text-[#2f3a1f]">{{ __('Maksājums atcelts') }}</h1>
        <p class="mt-4 text-neutral-600">{{ __('Jūsu rezervācija netika pabeigta. Datumi atkal būs pieejami pēc neilga brīža.') }}</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block text-[#2f3a1f] underline">{{ __('Atpakaļ uz sākumu') }}</a>
    </div>
</x-app-layout>
