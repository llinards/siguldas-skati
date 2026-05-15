<?php

namespace App\Services;

use App\Models\HeaderMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class HeaderMediaService
{
    public function __construct(private FileStorageService $fileStorageService) {}

    public function getAllOrdered(): Collection
    {
        return HeaderMedia::all();
    }

    public function store(UploadedFile $file): HeaderMedia
    {
        $isVideo = $this->isVideo($file);

        $path = $this->fileStorageService->storeFile(
            $file,
            $isVideo ? FileStorageService::HEADER_VIDEO_PATH : FileStorageService::HEADER_IMAGE_PATH
        );

        $nextOrder = (HeaderMedia::withoutGlobalScope('order')->max('order') ?? -1) + 1;

        return HeaderMedia::create([
            'filename' => $path,
            'order' => $nextOrder,
            'type' => $isVideo ? HeaderMedia::TYPE_VIDEO : HeaderMedia::TYPE_IMAGE,
        ]);
    }

    public function delete(int $id): bool
    {
        $media = HeaderMedia::findOrFail($id);
        $this->fileStorageService->deleteFile($media->filename);

        return $media->delete();
    }

    public function updateOrder(int $id, int $position): void
    {
        $media = HeaderMedia::findOrFail($id);
        $items = HeaderMedia::orderBy('order')->get()
            ->reject(fn ($i) => $i->id === $media->id)->values();
        $items->splice($position, 0, [$media]);

        foreach ($items as $index => $i) {
            if ($i->order !== $index) {
                $i->update(['order' => $index]);
            }
        }
    }

    public function isVideo(UploadedFile $file): bool
    {
        return Str::startsWith((string) $file->getMimeType(), 'video/');
    }

    public function maxSizeKbFor(UploadedFile $file): int
    {
        return $this->isVideo($file)
            ? FileStorageService::MAX_VIDEO_FILE_SIZE_KB
            : FileStorageService::MAX_FILE_SIZE_KB;
    }
}
