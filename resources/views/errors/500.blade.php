<x-error-layout>
    <x-slot name="title">404</x-slot>
    <h1 class="mb-2 text-center text-2xl">
        @lang('Ups... ir notikusi kļūda. :(')
    </h1>
    <div class="text-center">
        <x-btn-primary :type="'button'" onclick="window.history.back()">
            @lang('Atpakaļ')
        </x-btn-primary>
    </div>
</x-error-layout>
