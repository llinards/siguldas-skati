<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 block">
            <label class="mt-4 flex cursor-pointer space-x-2 self-start text-sm text-gray-600">
                <span class="relative">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="peer border-ss-dark bg-ss checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 transition duration-200"
                    />
                    <svg
                        class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                        fill="none"
                        viewBox="0 0 20 20"
                        stroke="currentColor"
                        stroke-width="3"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6" />
                    </svg>
                </span>
                <span>{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-end">
            @if (Route::has('password.request'))
                <a
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-btn-primary :type="'submit'" class="ms-3">
                {{ __('Log in') }}
            </x-btn-primary>
        </div>
    </form>
</x-guest-layout>
