<?php

namespace App\Livewire\Admin;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\NewsletterService;
use Livewire\Component;

class NewsletterList extends Component
{
    private NewsletterService $newsletterService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(NewsletterService $newsletterService, FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService): void
    {
        $this->newsletterService = $newsletterService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public object $subscribers;

    public function mount(): void
    {
        $this->subscribers = $this->newsletterService->getAllSubscribers();
    }

    public function destroy(int $subscriberId): void
    {
        try {
            $subscriber = $this->newsletterService->getSubscriberById($subscriberId);

            if (! $subscriber) {
                $this->flashMessageService->error(__('Pieteikums nav atrasts vai nevarēja tikt dzēsts.'));

                return;
            }

            $this->newsletterService->deleteSubscriberById($subscriber);
            $this->flashMessageService->success(__('Pieteikums veiksmīgi dzēsts.'));
            $this->mount();
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete subscriber in the newsletter list', $e, ['subscriber_id' => $subscriber]);
            $this->flashMessageService->error(__('Dzēšot pieteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function destroyAll(): void
    {
        try {

            $this->newsletterService->deleteAllSubscribers();
            $this->flashMessageService->success(__('Pieteikumi veiksmīgi dzēsti.'));
            $this->mount();
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete all subscribers in the newsletter list', $e, []);
            $this->flashMessageService->error(__('Dzēšot pieteikumus, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render()
    {
        return view('livewire.admin.newsletter-list')->layout('layouts.admin.app');
    }
}
