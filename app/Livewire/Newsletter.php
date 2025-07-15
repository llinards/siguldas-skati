<?php

namespace App\Livewire;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class Newsletter extends Component
{
    use UsesSpamProtection;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public HoneypotData $extraFields;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('accepted')]
    public bool $consent = false;

    public function boot(
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
    ): void {
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    protected function messages(): array
    {
        return [
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),

            'consent.accepted' => __('validation.consent.accepted'),
        ];
    }

    public function save(): void
    {
        $this->protectAgainstSpam();
        $this->validate();
        try {

            $this->flashMessageService->success(__('Paldies! Jūsu pieteikums ir saņemts.'));
            $this->reset();
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to subscribe user to newsletters.', $e, []);
            $this->flashMessageService->error(__('Notikusi kļūda! Mēģini vēlreiz vai sazinies ar mums pa telefonu.'));
        }
    }

    public function mount(): void
    {
        $this->extraFields = new HoneypotData;
    }

    public function render()
    {
        return <<<'HTML'
                <form class="flex w-full flex-col items-center justify-between xl:px-8" wire:submit.prevent="save">
                    <x-honeypot livewire-model="extraFields"/>
                    <x-admin.flash-message/>
                    <label for="email" class="mb-4 block self-start leading-none font-medium text-gray-500">
                        @lang('E-pasts')
                    </label>
                    <div class="w-full items-center sm:flex">
                        <input
                            type="email"
                            class="border-ss-dark mb-4 block w-full rounded-lg border-1 px-4 py-4 sm:mb-0 xl:ml-0"
                            placeholder="@lang('Ievadiet savu e-pastu')"
                            required="required"
                            wire:model="email"
                        />
                        <x-btn-primary
                            type="submit"
                            class="block w-full sm:ml-4 sm:w-auto"
                            :disabled="!$consent"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
                            <span wire:loading.remove wire:target="save">
                                @lang('Abonēt')
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center justify-center">
                                <svg
                                    class="mr-3 -ml-1 h-4 w-4 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </span>
                        </x-btn-primary>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('email')"/>
                    <label class="mt-4 flex cursor-pointer space-x-2 self-start text-sm text-gray-600">
                        <span class="relative">
                            <input
                                type="checkbox"
                                wire:model.live="consent"
                                class="peer border-ss-dark checked:bg-ss-dark checked:border-ss-dark h-5 w-5 appearance-none rounded border-1 bg-white transition duration-200"
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
                        <span>
                            @lang('Es piekrītu datu uzglabāšanai un apstrādei')
                        </span>
                    </label>
                </form>
        HTML;
    }
}
