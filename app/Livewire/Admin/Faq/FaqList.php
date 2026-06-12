<?php

namespace App\Livewire\Admin\Faq;

use App\Services\ErrorLogService;
use App\Services\FaqService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FaqList extends Component
{
    use WithPagination;

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

    public function deleteFaq(int $faqId): void
    {
        try {
            $faq = $this->faqService->getFaqById($faqId);

            if (! $faq) {
                $this->flashMessageService->error(__('Jautājums nav atrasts vai nevarēja tikt dzēsts.'));

                return;
            }

            $this->faqService->deleteFaq($faq);
            $this->flashMessageService->success(__('Jautājums veiksmīgi dzēsts.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete FAQ', $e, ['faq_id' => $faqId]);
            $this->flashMessageService->error(__('Dzēšot jautājumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $faqId): void
    {
        try {
            if ($this->faqService->toggleFaqStatus($faqId)) {
                $this->flashMessageService->success(__('Jautājuma statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Jautājums nav atrasts vai nevarēja tikt atjaunināts.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle FAQ status', $e, ['faq_id' => $faqId]);
            $this->flashMessageService->error(__('Atjauninot jautājuma statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateFaqOrder(string $id, int $position): void
    {
        try {
            $this->faqService->updateFaqOrder((int) $id, $position);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update FAQ order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $faqs = $this->faqService->getAllFaqs();

            return view('livewire.admin.faq.faq-list', compact('faqs'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load FAQ list', $e, []);
            $this->flashMessageService->error(__('Ielādējot jautājumus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.faq.faq-list', [
                'faqs' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
