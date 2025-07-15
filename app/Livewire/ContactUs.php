<?php

namespace App\Livewire;

use App\Mail\ContactUsMail;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class ContactUs extends Component
{
    use UsesSpamProtection;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public HoneypotData $extraFields;

    #[Validate('required|min:2|max:50|regex:/^[a-zA-ZāčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ\s\-\']+$/')]
    public string $firstName = '';

    #[Validate('required|min:2|max:50|regex:/^[a-zA-ZāčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ\s\-\']+$/')]
    public string $lastName = '';

    #[Validate('required|regex:/^[\+]?[0-9\s\-\(\)]+$/|min:8|max:20')]
    public string $phoneNumber = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('max:2000')]
    public string $question = '';

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
            'firstName.required' => __('validation.first_name.required'),
            'firstName.min' => __('validation.first_name.min'),
            'firstName.max' => __('validation.first_name.max'),
            'firstName.regex' => __('validation.first_name.regex'),

            'lastName.required' => __('validation.last_name.required'),
            'lastName.min' => __('validation.last_name.min'),
            'lastName.max' => __('validation.last_name.max'),
            'lastName.regex' => __('validation.last_name.regex'),

            'phoneNumber.required' => __('validation.phone.required'),
            'phoneNumber.regex' => __('validation.phone.regex'),
            'phoneNumber.min' => __('validation.phone.min'),
            'phoneNumber.max' => __('validation.phone.max'),

            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),

            'question.max' => __('validation.question.max'),

            'consent.accepted' => __('validation.consent.accepted'),
        ];
    }

    public function save(): void
    {
        $this->protectAgainstSpam();
        $this->validate();
        try {
            Mail::send(new ContactUsMail(
                $this->firstName,
                $this->lastName,
                $this->phoneNumber,
                $this->email,
                $this->question,
                request()->ip()
            ));

            $this->flashMessageService->success(__('Paldies! Jūsu ziņa ir saņemta.'));
            $this->reset();
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to send message from contact us form.', $e, []);
            $this->flashMessageService->error(__('Notikusi kļūda! Mēģini vēlreiz vai sazinies ar mums pa telefonu.'));
        }
    }

    public function mount()
    {
        $this->extraFields = new HoneypotData;
    }

    public function render(): View
    {
        return view('livewire.contact-us');
    }
}
