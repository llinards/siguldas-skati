<?php

namespace App\Livewire\Admin\Faq;

use App\Services\ErrorLogService;
use App\Services\FaqService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddFaq extends Component
{
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

    public function save(): void
    {
        $this->validate();

        try {
            $this->createFaq();
            $this->flashMessageService->success(__('Jautājums veiksmīgi izveidots.'));
            $this->redirect(route('dashboard.faqs'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to create FAQ', $e, []);
            $this->flashMessageService->error(__('Veidojot jautājumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function createFaq(): void
    {
        $this->faqService->createFaq($this->prepareFaqData());
    }

    private function prepareFaqData(): array
    {
        return [
            'question' => $this->question,
            'answer' => $this->answer,
            'is_active' => $this->is_active,
            'order' => $this->faqService->getAllFaqs()->count() + 1,
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.faq.add-faq')
            ->layout('layouts.admin.app');
    }
}
