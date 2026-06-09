<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\SiteSettingService;
use Illuminate\View\View;
use Livewire\Component;

class HomeHeroSettings extends Component
{
    public string $title = '';

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
        $this->title = $this->siteSettingService->get(SiteSetting::KEY_HOME_HERO_TITLE) ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => __('Lūdzu, ievadiet virsrakstu.'),
            'title.max' => __('Virsraksts nedrīkst pārsniegt :max rakstzīmes.'),
        ]);

        try {
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_HOME_HERO_TITLE, $this->title);

            $this->flashMessageService->success(__('Virsraksts atjaunināts.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save home hero title.', $e, []);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.home-hero-settings');
    }
}
