<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class GalleryServices
{
    public function __construct(private FileStorageService $fileStorageService) {}

    public function getAllGalleries(): Collection
    {
        return Gallery::all();
    }

    public function getAllActiveGalleries(): Collection
    {
        return Gallery::all()->where('is_active', 1);
    }

    public function getGalleryById(int $id): ?Gallery
    {
        return Gallery::find($id);
    }

    public function createGallery(array $data): Gallery
    {
        return Gallery::create($data);
    }

    public function updateGallery(Gallery $gallery, array $data): bool
    {
        return $gallery->update($data);
    }

    public function deleteGallery(Gallery $gallery): bool
    {
        foreach ($gallery->images as $image) {
            if ($image->filename) {
                $this->fileStorageService->deleteFile($image->filename);
            }
        }

        return $gallery->delete();
    }

    public function toggleGalleryStatus(int $galleryId): bool
    {
        $gallery = Gallery::find($galleryId);
        if (! $gallery) {
            return false;
        }

        return $gallery->update(['is_active' => ! $gallery->is_active]);
    }

    public function storeGalleryImage(int $galleryId, UploadedFile $file): GalleryImage
    {
        $path = $this->fileStorageService->storeFile(
            $file,
            FileStorageService::GALLERY_IMAGE_PATH
        );

        return GalleryImage::create([
            'gallery_id' => $galleryId,
            'filename' => $path,
        ]);
    }

    public function deleteGalleryImage(int $imageId): bool
    {
        $image = GalleryImage::findOrFail($imageId);
        $this->fileStorageService->deleteFile($image->filename);

        return $image->delete();
    }

    public function updateImageOrder(array $images): void
    {
        foreach ($images as $image) {
            GalleryImage::findOrFail($image['value'])
                ->update(['order' => $image['order']]);
        }
    }
}
