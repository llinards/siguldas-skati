<?php

namespace App\Livewire\Admin\Experience;

use App\Models\Experience;
use App\Services\ErrorLogService;
use App\Services\ExperienceService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditExperience extends Component
{
    use WithFileUploads;

    public Experience $experience;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $description = '';

    #[Validate('nullable')]
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

    public function mount(Experience $experience): void
    {
        $this->experience = $experience;
        $this->title = $this->experience->title;
        $this->description = $this->experience->description;
        $this->is_active = $this->experience->is_active;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->updateExperience();
            $this->flashMessageService->success(__('Pieredze veiksmīgi atjaunināta.'));
            $this->redirect(route('dashboard.experiences'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update experience', $e, [
                'experience_id' => $this->experience->id,
            ]);
            $this->flashMessageService->error(__('Atjauninot pieredzi, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function updateExperience(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->experienceService->updateExperience($this->experience, $updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
        if ($this->hasNewIcon()) {
            $updateData['icon_image'] = $this->experienceService->updateExperienceIcon($this->experience, $this->icon_image);
        }

        return $updateData;
    }

    private function hasNewIcon(): bool
    {
        return $this->icon_image !== null;
    }

    public function render(): View
    {
        return view('livewire.admin.experience.edit-experience')
            ->layout('layouts.admin.app');
    }
}
