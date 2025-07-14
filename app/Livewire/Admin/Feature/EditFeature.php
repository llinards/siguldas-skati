<?php

namespace App\Livewire\Admin\Feature;

use App\Models\Feature;
use App\Services\ErrorLogService;
use App\Services\FeatureService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFeature extends Component
{
    use WithFileUploads;

    public Feature $feature;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('nullable')]
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

    public function mount(Feature $feature): void
    {
        $this->feature = $feature;
        $this->title = $this->feature->title;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->updateFeature();
            $this->flashMessageService->success(__('Funkcija veiksmīgi atjaunināta.'));
            $this->redirect(route('dashboard.features'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update feature', $e, [
                'feature_id' => $this->feature->id,
            ]);
            $this->flashMessageService->error(__('Atjauninot funkciju, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function updateFeature(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->featureService->updateFeature($this->feature, $updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];
        if ($this->hasNewIcon()) {
            $updateData['icon_image'] = $this->featureService->updateFeatureIcon($this->feature, $this->icon_image);
        }

        return $updateData;
    }

    private function hasNewIcon(): bool
    {
        return $this->icon_image !== null;
    }

    public function render(): View
    {
        return view('livewire.admin.feature.edit-feature')
            ->layout('layouts.admin.app');
    }
}
