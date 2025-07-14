<?php

namespace App\Livewire\Admin\Feature;

use App\Services\ErrorLogService;
use App\Services\FeatureService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFeature extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    #[Validate('max:10', message: 'Bildes izmērs nedrīkst pārsniegt 10 kb.')]
    #[Validate('mimes:svg,png', message: 'Drīkst pievienot tikai SVG vai PNG formāta ikonas.')]
    public $icon_image;

    public bool $is_active = true;

    private FeatureService $featureService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private FileStorageService $fileStorageService;

    public function boot(
        FeatureService $featureService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
        FileStorageService $fileStorageService
    ): void {
        $this->featureService = $featureService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
        $this->fileStorageService = $fileStorageService;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->createFeature();
            $this->flashMessageService->success(__('Funkcija veiksmīgi izveidota.'));
            $this->redirect(route('dashboard.features'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to create feature', $e, []);
            $this->flashMessageService->error(__('Veidojot funkciju, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function createFeature(): void
    {
        $featureData = $this->prepareFeatureData();
        $this->featureService->createFeature($featureData);
    }

    private function prepareFeatureData(): array
    {
        if ($this->icon_image) {
            $icon = $this->featureService->storeFeatureIcon($this->icon_image);
        }

        return [
            'title' => $this->title,
            'is_active' => $this->is_active,
            'order' => $this->featureService->getAllFeatures()->count() + 1,
            'icon_image' => $icon,
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.feature.add-feature')
            ->layout('layouts.admin.app');
    }
}
