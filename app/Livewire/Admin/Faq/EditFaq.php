<?php

namespace App\Livewire\Admin\Faq;

use App\Models\Faq;
use App\Services\ErrorLogService;
use App\Services\FaqService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditFaq extends Component
{
    public Faq $faq;

    #[Validate('required', message: 'validation.required')]
    public string $question = '';

    #[Validate('required', message: 'validation.required')]
    public string $answer = '';

    public bool $is_active = true;

    private FaqService $faqService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        FaqService $faqService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->faqService = $faqService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function mount(Faq $faq): void
    {
        $this->faq = $faq;
        $this->question = $this->faq->question;
        $this->answer = $this->faq->answer;
        $this->is_active = $this->faq->is_active;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->faqService->updateFaq($this->faq, [
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->is_active,
            ]);
            $this->flashMessageService->success(__('Jautājums veiksmīgi atjaunināts.'));
            $this->redirect(route('dashboard.faqs'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update FAQ', $e, [
                'faq_id' => $this->faq->id,
            ]);
            $this->flashMessageService->error(__('Atjauninot jautājumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.faq.edit-faq')
            ->layout('layouts.admin.app');
    }
}
