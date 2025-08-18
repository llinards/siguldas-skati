<?php

namespace App\Livewire\Admin\Gallery;

use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use App\Services\GalleryServices;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class GalleryImages extends Component
{
    use WithFileUploads;

    public $gallery;

    public array $images = [];

    private GalleryServices $galleryServices;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private FileStorageService $fileStorageService;

    public function boot(
        GalleryServices $galleryServices,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
        FileStorageService $fileStorageService
    ): void {
        $this->galleryServices = $galleryServices;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
        $this->fileStorageService = $fileStorageService;
    }

    public function mount($gallery): void
    {
        $this->gallery = $this->galleryServices->getGalleryById($gallery);
    }

    public function updateImageOrder(array $images): void
    {
        try {
            $this->galleryServices->updateImageOrder($images);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update gallery image order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function isImageOversized(int $index): bool
    {
        if (! isset($this->images[$index])) {
            return false;
        }

        return $this->fileStorageService->isFileSizeExceeded($this->images[$index]);
    }

    public function getImageSizeInKB(int $index): int
    {
        if (! isset($this->images[$index])) {
            return 0;
        }

        return $this->fileStorageService->getFileSizeInKB($this->images[$index]);
    }

    public function updatedImages(): void
    {
        $oversizedCount = collect($this->images)
            ->filter(fn ($image, $index) => $this->isImageOversized($index))
            ->count();

        if ($oversizedCount > 0) {
            $this->flashMessageService->error(__('Attēli ar sarkanu apmali pārsniedz :size KB ierobežojumu un netiks saglabāti.',
                [
                    'size' => FileStorageService::MAX_FILE_SIZE_KB,
                ]));
        } else {
            session()->forget('error');
        }
    }

    public function store(): void
    {
        $this->validateImages();

        try {
            $this->storeImages();
            $this->resetForm();
            $this->flashMessageService->success(__('Galerija veiksmīgi atjaunināta!'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save gallery.', $e, [
                'gallery_id' => $this->gallery->id,
            ]);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeImage(int $imageId): void
    {
        try {
            $this->galleryServices->deleteGalleryImage($imageId);
            $this->flashMessageService->success(__('Attēls dzēsts!'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete gallery image', $e, [
                'image_id' => $imageId,
                'gallery_id' => $this->gallery->id,
            ]);
            $this->flashMessageService->error(__('Radās kļūda dzēšot attēlu. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeNewImage(int $index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
        $this->updatedImages();
    }

    public function render(): View
    {
        return view('livewire.admin.gallery.gallery-images')
            ->layout('layouts.admin.app');
    }

    private function validateImages(): void
    {
        $this->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:'.FileStorageService::MAX_FILE_SIZE_KB,
        ], [
            'images.required' => 'Lūdzu, izvēlies vismaz vienu attēlu.',
            'images.*.image' => 'Katram failam jābūt attēlam.',
            'images.*.max' => 'Attēls nedrīkst pārsniegt :max KB.',
        ]);
    }

    private function storeImages(): void
    {
        foreach ($this->images as $image) {
            $this->galleryServices->storeGalleryImage($this->gallery->id, $image);
        }
    }

    private function resetForm(): void
    {
        $this->images = [];
    }
}
