<form wire:submit.prevent="save" class="md:w-4/5 lg:w-full">
    <x-admin.flash-message/>
    <div
        class="border-ss-gray text-ss-gray mb-6 flex flex-col space-y-6 rounded-3xl border-1 p-6 shadow-md"
    >
        <label class="mb-2" for="firstName">
            @lang('Vārds')
            *
        </label>
        <x-input-error :messages="$errors->get('firstName')"/>
        <input class="border-b" id="firstName" wire:model="firstName" type="text"/>
        <label class="mb-2" for="lastName">
            @lang('Uzvārds')
            *
        </label>
        <x-input-error :messages="$errors->get('lastName')"/>
        <input class="border-b" id="lastName" wire:model="lastName" type="text"/>
        <label class="mb-2" for="phoneNumber">
            @lang('Telefons')
            *
        </label>
        <x-input-error :messages="$errors->get('phoneNumber')"/>
        <input class="border-b" id="phoneNumber" wire:model="phoneNumber" type="phoneNumber"/>
        <label class="mb-2" for="email">
            @lang('E-pasts')
            *
        </label>
        <x-input-error :messages="$errors->get('email')"/>
        <input class="border-b" id="email" wire:model="email" type="email"/>
        <label class="mb-2" for="question">
            @lang('Jautājums')
        </label>
        <textarea
            class="w-full resize-none border-b"
            rows="3"
            cols="30"
            id="question"
            wire:model="question"
        ></textarea>
        <label class="mt-2 flex cursor-pointer space-x-2 self-start text-sm text-gray-600">
                                <span class="relative">
                                    <input
                                        type="checkbox"
                                        wire:model.live="consent"
                                        class="peer border-ss-dark bg-ss checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 transition duration-200"
                                    />
                                    <svg
                                        class="pointer-events-none absolute top-0 left-0 h-5 w-5 text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100"
                                        fill="none"
                                        viewBox="0 0 20 20"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l4 4 6-6"/>
                                    </svg>
                                </span>
            <span>
                                    @lang('Es piekrītu datu uzglabāšanai un apstrādei')
                                </span>
        </label>
    </div>
    <div class="flex w-full">
        <x-btn-primary
            type="submit"
            class="w-full"
            :disabled="!$consent"
            wire:loading.attr="disabled"
            wire:target="save"
        >
    <span wire:loading.remove wire:target="save">
        @lang('Nosūtīt')
    </span>
            <span wire:loading wire:target="save" class="flex items-center justify-center">
        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </span>
        </x-btn-primary>
    </div>
</form>
