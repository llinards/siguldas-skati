<?php

namespace App\Livewire\Admin\Activity;

use App\Services\ActivityService;
use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddActivity extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $modal_heading = '';

    #[Validate('required', message: 'validation.required')]
    public string $modal_content = '';

    #[Validate('required', message: 'validation.required')]
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

    public function save(): void
    {
        $this->validate();

        try {
            $this->createActivity();
            $this->flashMessageService->success(__('Aktivitāte veiksmīgi izveidota.'));
            $this->redirect(route('dashboard.activities'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to create activity', $e, []);
            $this->flashMessageService->error(__('Veidojot aktivitāti, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function createActivity(): void
    {
        $activityData = $this->prepareActivityData();
        $this->activityService->createActivity($activityData);
    }

    private function prepareActivityData(): array
    {
        return [
            'title' => $this->title,
            'modal_heading' => $this->modal_heading,
            'modal_content' => $this->modal_content,
            'is_active' => $this->is_active,
            'order' => $this->activityService->getAllActivities()->count() + 1,
            'image' => $this->activityService->storeActivityImage($this->image),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.activity.add-activity')
            ->layout('layouts.admin.app');
    }
}
