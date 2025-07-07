<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Dzēst kontu') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Kad jūsu konts tiks dzēsts, visi tā resursi un dati tiks neatgriezeniski dzēsti. Pirms konta dzēšanas, lūdzu, lejupielādējiet jebkādus datus vai informāciju, ko vēlaties saglabāt.') }}
        </p>
    </header>

    <x-btn-danger x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        {{ __('Dzēst kontu') }}
    </x-btn-danger>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Vai esat pārliecināts, ka vēlaties dzēst savu kontu?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Kad jūsu konts tiks dzēsts, visi tā resursi un dati tiks neatgriezeniski dzēsti. Lūdzu, ievadiet savu paroli, lai apstiprinātu, ka vēlaties neatgriezeniski dzēst savu kontu.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Parole') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Parole') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Atcelt') }}
                </x-secondary-button>

                <x-btn-danger class="ms-3">
                    {{ __('Dzēst kontu') }}
                </x-btn-danger>
            </div>
        </form>
    </x-modal>
</section>
