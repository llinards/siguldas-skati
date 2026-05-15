<?php

namespace App\Services;

use App\Models\HeaderImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class HeaderImageService
{
    public function __construct(private FileStorageService $fileStorageService) {}

    public function getAllOrdered(): Collection
    {
        return HeaderImage::all();
    }

    public function store(UploadedFile $file): HeaderImage
    {
        $path = $this->fileStorageService->storeFile(
            $file,
            FileStorageService::HEADER_IMAGE_PATH
        );

        $nextOrder = (HeaderImage::withoutGlobalScope('order')->max('order') ?? -1) + 1;

        return HeaderImage::create([
            'filename' => $path,
            'order' => $nextOrder,
        ]);
    }

    public function delete(int $id): bool
    {
        $image = HeaderImage::findOrFail($id);
        $this->fileStorageService->deleteFile($image->filename);

        return $image->delete();
    }

    public function updateOrder(int $id, int $position): void
    {
        $image = HeaderImage::findOrFail($id);
        $images = HeaderImage::orderBy('order')->get()
            ->reject(fn ($i) => $i->id === $image->id)->values();
        $images->splice($position, 0, [$image]);

        foreach ($images as $index => $i) {
            if ($i->order !== $index) {
                $i->update(['order' => $index]);
            }
        }
    }
}
