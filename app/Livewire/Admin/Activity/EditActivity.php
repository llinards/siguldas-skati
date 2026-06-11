<?php

namespace App\Livewire\Admin\Activity;

use App\Models\Activity;
use App\Services\ActivityService;
use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditActivity extends Component
{
    use WithFileUploads;

    public Activity $activity;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $modal_heading = '';

    #[Validate('required', message: 'validation.required')]
    public string $modal_content = '';

    #[Validate('nullable')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $image;

    public bool $is_active = true;

    private ActivityService $activityService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private FileStorageService $fileStorageService;

    public function boot(
        ActivityService $activityService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
        FileStorageService $fileStorageService
    ): void {
        $this->activityService = $activityService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
        $this->fileStorageService = $fileStorageService;
    }

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
        $this->title = $this->activity->title;
        $this->modal_heading = $this->activity->modal_heading;
        $this->modal_content = $this->activity->modal_content;
        $this->is_active = $this->activity->is_active;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->updateActivity();
            $this->flashMessageService->success(__('Aktivitāte veiksmīgi atjaunināta.'));
            $this->redirect(route('dashboard.activities'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update activity', $e, [
                'activity_id' => $this->activity->id,
            ]);
            $this->flashMessageService->error(__('Atjauninot aktivitāti, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function updateActivity(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->activityService->updateActivity($this->activity, $updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title' => $this->title,
            'modal_heading' => $this->modal_heading,
            'modal_content' => $this->modal_content,
            'is_active' => $this->is_active,
        ];
        if ($this->hasNewImage()) {
            $updateData['image'] = $this->activityService->updateActivityImage($this->activity, $this->image);
        }

        return $updateData;
    }

    private function hasNewImage(): bool
    {
        return $this->image !== null;
    }

    public function render(): View
    {
        return view('livewire.admin.activity.edit-activity')
            ->layout('layouts.admin.app');
    }
}
