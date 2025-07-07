<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profila informācija') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Atjauniniet sava konta profila informāciju un e-pasta adresi.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Vārds')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-pasts')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-gray-800">
                        {{ __('Jūsu e-pasta adrese nav verificēta.') }}

                        <button
                            form="send-verification"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                        >
                            {{ __('Noklikšķiniet šeit, lai atkārtoti nosūtītu verificēšanas e-pastu.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            {{ __('Jaunā verificēšanas saite ir nosūtīta uz jūsu e-pasta adresi.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-btn-primary :type="'submit'">
                {{ __('Saglabāt') }}
            </x-btn-primary>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => (show = false), 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Saglabāts.') }}
                </p>
            @endif
        </div>
    </form>
</section>
