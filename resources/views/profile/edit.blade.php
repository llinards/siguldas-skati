<x-admin.app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    <div class="max-w-xl">
        <div class="mb-10">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="mb-10">
            @include('profile.partials.update-password-form')
        </div>
        <div class="mb-10">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-admin.app-layout>
