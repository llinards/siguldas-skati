<?php

namespace App\Livewire\Admin\Gallery;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\GalleryServices;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddGallery extends Component
{
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

    public function store(): void
    {
        $this->validate();

        try {
            $this->createGallery();
            $this->flashMessageService->success(__('Galerija pievienota.'));
            $this->redirect(route(self::SUCCESS_ROUTE));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to store gallery.', $e, ['title' => $this->title]);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.gallery.add-gallery')
            ->layout('layouts.admin.app');
    }

    private function createGallery(): void
    {
        $galleryData = $this->prepareGalleryData();
        $this->galleryServices->createGallery($galleryData);
    }

    private function prepareGalleryData(): array
    {
        return [
            'title' => $this->title,
            'is_active' => $this->is_active,
        ];
    }
}
