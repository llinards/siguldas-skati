<?php

namespace App\Livewire\Admin\Gallery;

use App\Models\Gallery;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\GalleryServices;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditGallery extends Component
{
    public Gallery $gallery;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    public bool $is_active = false;

    private GalleryServices $galleryServices;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private const SUCCESS_ROUTE = 'dashboard.galleries';

    public function boot(
        GalleryServices $galleryServices,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->galleryServices = $galleryServices;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function mount(Gallery $gallery): void
    {
        $this->gallery = $gallery;
        $this->title = $gallery->title;
        $this->is_active = (bool) $gallery->is_active;
    }

    public function update(): void
    {
        $this->validate();

        try {
            $this->updateGallery();
            $this->flashMessageService->success(__('Galerija atjaunināta.'));
            $this->redirect(route(self::SUCCESS_ROUTE));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update gallery.', $e, ['gallery_id' => $this->gallery->id]);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.gallery.edit-gallery')
            ->layout('layouts.admin.app');
    }

    private function updateGallery(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->galleryServices->updateGallery($this->gallery, $updateData);
    }

    private function prepareUpdateData(): array
    {
        return [
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];
    }
}
