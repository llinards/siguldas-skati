<?php

namespace App\Livewire\Admin;

use App\Services\ErrorLogService;
use App\Services\FileStorageService;
use App\Services\FlashMessageService;
use App\Services\HeaderMediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class HeaderMedia extends Component
{
    use WithFileUploads;

    public array $media = [];

    private HeaderMediaService $headerMediaService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private FileStorageService $fileStorageService;

    public function boot(
        HeaderMediaService $headerMediaService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService,
        FileStorageService $fileStorageService
    ): void {
        $this->headerMediaService = $headerMediaService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
        $this->fileStorageService = $fileStorageService;
    }

    public function updateMediaOrder(string $id, int $position): void
    {
        try {
            $this->headerMediaService->updateOrder((int) $id, $position);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update header media order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function isMediaOversized(int $index): bool
    {
        if (! isset($this->media[$index])) {
            return false;
        }

        $file = $this->media[$index];

        return $this->fileStorageService->isFileSizeExceeded(
            $file,
            $this->headerMediaService->maxSizeKbFor($file)
        );
    }

    public function isMediaVideo(int $index): bool
    {
        if (! isset($this->media[$index])) {
            return false;
        }

        return $this->headerMediaService->isVideo($this->media[$index]);
    }

    public function getMediaSizeInKB(int $index): int
    {
        if (! isset($this->media[$index])) {
            return 0;
        }

        return $this->fileStorageService->getFileSizeInKB($this->media[$index]);
    }

    public function updatedMedia(): void
    {
        $oversizedCount = collect($this->media)
            ->filter(fn ($file, $index) => $this->isMediaOversized($index))
            ->count();

        if ($oversizedCount > 0) {
            $this->flashMessageService->error(__('Faili ar sarkanu apmali pārsniedz izmēra ierobežojumu (attēli :imgKB KB, video :vidKB KB) un netiks saglabāti.', [
                'imgKB' => FileStorageService::MAX_FILE_SIZE_KB,
                'vidKB' => FileStorageService::MAX_VIDEO_FILE_SIZE_KB,
            ]));
        } else {
            session()->forget('error');
        }
    }

    public function store(): void
    {
        $this->validateMedia();

        try {
            $this->storeMedia();
            $this->resetForm();
            $this->flashMessageService->success(__('Galvenais karuselis veiksmīgi atjaunināts!'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save header media.', $e, []);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeMedia(int $mediaId): void
    {
        try {
            $this->headerMediaService->delete($mediaId);
            $this->flashMessageService->success(__('Fails dzēsts!'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete header media', $e, [
                'media_id' => $mediaId,
            ]);
            $this->flashMessageService->error(__('Radās kļūda dzēšot failu. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeNewMedia(int $index): void
    {
        unset($this->media[$index]);
        $this->media = array_values($this->media);
        $this->updatedMedia();
    }

    public function render(): View
    {
        return view('livewire.admin.header-media', [
            'headerMedia' => $this->headerMediaService->getAllOrdered(),
        ])->layout('layouts.admin.app');
    }

    private function validateMedia(): void
    {
        $this->validate([
            'media' => 'required|array',
            'media.*' => [
                'file',
                function (string $attribute, $value, \Closure $fail) {
                    if (! $value instanceof UploadedFile) {
                        $fail(__('Nederīgs fails.'));

                        return;
                    }

                    $mime = (string) $value->getMimeType();

                    if ($this->headerMediaService->isVideo($value)) {
                        if (! in_array($mime, ['video/mp4'], true)) {
                            $fail(__('Video failam jābūt MP4 formātā.'));

                            return;
                        }

                        if ($this->fileStorageService->isFileSizeExceeded($value, FileStorageService::MAX_VIDEO_FILE_SIZE_KB)) {
                            $fail(__('Video nedrīkst pārsniegt :max KB.', ['max' => FileStorageService::MAX_VIDEO_FILE_SIZE_KB]));
                        }

                        return;
                    }

                    if (! str_starts_with($mime, 'image/')) {
                        $fail(__('Faila tipam jābūt attēlam vai MP4 video.'));

                        return;
                    }

                    if ($this->fileStorageService->isFileSizeExceeded($value, FileStorageService::MAX_FILE_SIZE_KB)) {
                        $fail(__('Attēls nedrīkst pārsniegt :max KB.', ['max' => FileStorageService::MAX_FILE_SIZE_KB]));
                    }
                },
            ],
        ], [
            'media.required' => 'Lūdzu, izvēlies vismaz vienu failu.',
        ]);
    }

    private function storeMedia(): void
    {
        foreach ($this->media as $file) {
            $this->headerMediaService->store($file);
        }
    }

    private function resetForm(): void
    {
        $this->media = [];
    }
}
