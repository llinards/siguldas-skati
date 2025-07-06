<x-error-layout>
    <x-slot name="title">503</x-slot>
    <h1 class="mb-2 text-center text-2xl">
        @lang('Notiek modernizācijas darbi!')
    </h1>
    <h2 class="mb-4 text-center text-md">@lang('Šobrīd mājaslapa nav pieejama, taču drīz būsim atpakaļ!')</h2>
    <div class="text-center">
        <x-btn-primary :type="'button'" onclick="location.reload()">@lang('Atjaunot')</x-btn-primary>
    </div>
</x-error-layout>
