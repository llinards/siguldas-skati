<?php

namespace App\Livewire\Admin\Gallery;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\GalleryServices;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GalleryList extends Component
{
    use WithPagination;

    private GalleryServices $galleryServices;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        GalleryServices $galleryServices,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->galleryServices = $galleryServices;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteGallery(int $galleryId): void
    {
        try {
            $gallery = $this->galleryServices->getGalleryById($galleryId);

            if (! $gallery) {
                $this->flashMessageService->error(__('Galerija nav atrasta vai nevarēja tikt dzēsta.'));

                return;
            }

            $this->galleryServices->deleteGallery($gallery);
            $this->flashMessageService->success(__('Galerija veiksmīgi dzēsta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete gallery', $e, ['gallery_id' => $galleryId]);
            $this->flashMessageService->error(__('Dzēšot galeriju, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $galleryId): void
    {
        try {
            if ($this->galleryServices->toggleGalleryStatus($galleryId)) {
                $this->flashMessageService->success(__('Galerijas statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Galerija nav atrasta vai nevarēja tikt atjaunināta.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle gallery status', $e, ['gallery_id' => $galleryId]);
            $this->flashMessageService->error(__('Atjauninot galerijas statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateGalleryOrder(array $galleries): void
    {
        try {
            foreach ($galleries as $galleryData) {
                \App\Models\Gallery::findOrFail($galleryData['value'])
                    ->update(['order' => $galleryData['order']]);
            }
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update gallery order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $galleries = $this->galleryServices->getAllGalleries();

            return view('livewire.admin.gallery.gallery-list', compact('galleries'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load galleries list', $e, []);
            $this->flashMessageService->error(__('Ielādējot galerijas, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.gallery.gallery-list', [
                'galleries' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
