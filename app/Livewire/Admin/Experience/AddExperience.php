<?php

namespace App\Livewire\Admin\Experience;

use App\Services\ErrorLogService;
use App\Services\ExperienceService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddExperience extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $description = '';

    #[Validate('required', message: 'validation.required')]
    #[Validate('max:10', message: 'Bildes izmērs nedrīkst pārsniegt 10 kb.')]
    #[Validate('mimes:svg,png', message: 'Drīkst pievienot tikai SVG vai PNG formāta ikonas.')]
    public $icon_image;

    public bool $is_active = true;

    private ExperienceService $experienceService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private FileStorageService $fileStorageService;

    public function boot(
        ExperienceService $experienceService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
        FileStorageService $fileStorageService
    ): void {
        $this->experienceService = $experienceService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
        $this->fileStorageService = $fileStorageService;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->createExperience();
            $this->flashMessageService->success(__('Pieredze veiksmīgi izveidota.'));
            $this->redirect(route('dashboard.experiences'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to create experience', $e, []);
            $this->flashMessageService->error(__('Veidojot pieredzi, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function createExperience(): void
    {
        $experienceData = $this->prepareExperienceData();
        $this->experienceService->createExperience($experienceData);
    }

    private function prepareExperienceData(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'order' => $this->experienceService->getAllExperiences()->count() + 1,
            'icon_image' => $this->experienceService->storeExperienceIcon($this->icon_image),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.experience.add-experience')
            ->layout('layouts.admin.app');
    }
}
