<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\SiteSettingService;
use Illuminate\View\View;
use Livewire\Component;

class ProductSectionSettings extends Component
{
    public string $title = '';

    public string $subtitle = '';

    private SiteSettingService $siteSettingService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        SiteSettingService $siteSettingService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
    ): void {
        $this->siteSettingService = $siteSettingService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function mount(): void
    {
        $this->title = $this->siteSettingService->get(SiteSetting::KEY_PRODUCTS_TITLE) ?? '';
        $this->subtitle = $this->siteSettingService->get(SiteSetting::KEY_PRODUCTS_SUBTITLE) ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
        ], [
            'title.required' => __('Lūdzu, ievadiet virsrakstu.'),
            'subtitle.required' => __('Lūdzu, ievadiet apakšvirsrakstu.'),
        ]);

        try {
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_PRODUCTS_TITLE, $this->title);
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_PRODUCTS_SUBTITLE, $this->subtitle);

            $this->flashMessageService->success(__('Sadaļa "Mājas" atjaunināta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save product section settings.', $e, []);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.product-section-settings');
    }
}
