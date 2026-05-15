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
    public string $titleLv = '';

    public string $titleEn = '';

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
        $translations = $this->siteSettingService->getTranslations(SiteSetting::KEY_HOME_HERO_TITLE);

        $this->titleLv = $translations['lv'] ?? '';
        $this->titleEn = $translations['en'] ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'titleLv' => 'required|string|max:255',
            'titleEn' => 'required|string|max:255',
        ], [
            'titleLv.required' => __('Lūdzu, ievadiet virsrakstu latviešu valodā.'),
            'titleEn.required' => __('Lūdzu, ievadiet virsrakstu angļu valodā.'),
            'titleLv.max' => __('Virsraksts nedrīkst pārsniegt :max rakstzīmes.'),
            'titleEn.max' => __('Virsraksts nedrīkst pārsniegt :max rakstzīmes.'),
        ]);

        try {
            $this->siteSettingService->set(SiteSetting::KEY_HOME_HERO_TITLE, [
                'lv' => $this->titleLv,
                'en' => $this->titleEn,
            ]);

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
