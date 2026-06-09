<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use App\Services\SiteSettingService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class AboutUsSettings extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $subtitle = '';

    public string $heading = '';

    public string $description = '';

    public ?TemporaryUploadedFile $image = null;

    public ?string $currentImage = null;

    private SiteSettingService $siteSettingService;

    private FileStorageService $fileStorageService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        SiteSettingService $siteSettingService,
        FileStorageService $fileStorageService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
    ): void {
        $this->siteSettingService = $siteSettingService;
        $this->fileStorageService = $fileStorageService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function mount(): void
    {
        $this->title = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_TITLE) ?? '';
        $this->subtitle = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_SUBTITLE) ?? '';
        $this->heading = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_HEADING) ?? '';
        $this->description = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_DESCRIPTION) ?? '';
        $this->currentImage = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_IMAGE);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'heading' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image',
        ], [
            'title.required' => __('Lūdzu, ievadiet virsrakstu.'),
            'subtitle.required' => __('Lūdzu, ievadiet apakšvirsrakstu.'),
            'heading.required' => __('Lūdzu, ievadiet sadaļas virsrakstu.'),
            'description.required' => __('Lūdzu, ievadiet aprakstu.'),
            'image.image' => __('Faila tipam jābūt attēlam.'),
        ]);

        if ($this->image !== null && $this->fileStorageService->isFileSizeExceeded($this->image)) {
            $this->addError('image', __('Attēls nedrīkst pārsniegt :max KB.', ['max' => FileStorageService::MAX_FILE_SIZE_KB]));

            return;
        }

        try {
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_ABOUT_TITLE, $this->title);
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_ABOUT_SUBTITLE, $this->subtitle);
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_ABOUT_HEADING, $this->heading);
            $this->siteSettingService->setForCurrentLocale(SiteSetting::KEY_ABOUT_DESCRIPTION, $this->description);

            if ($this->image !== null) {
                $path = $this->fileStorageService->storeFile($this->image, FileStorageService::ABOUT_IMAGE_PATH);

                if ($this->currentImage !== null) {
                    $this->fileStorageService->deleteFile($this->currentImage);
                }

                $this->siteSettingService->setForAllLocales(SiteSetting::KEY_ABOUT_IMAGE, $path);

                $this->currentImage = $path;
                $this->image = null;
            }

            $this->flashMessageService->success(__('Sadaļa "Par mums" atjaunināta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save about-us settings.', $e, []);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.about-us-settings');
    }
}
