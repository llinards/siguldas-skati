<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    private const DEFAULT_DISK = 'public';

    public const PRODUCT_IMAGE_PATH = 'product-images';

    public const PRODUCT_GALLERY_PATH = 'product-images/gallery';

    public const FEATURE_ICON_PATH = 'feature-icons';

    public const MAX_FILE_SIZE_KB = 512;

    public function storeFile(UploadedFile $file, string $path, string $disk = self::DEFAULT_DISK): string
    {
        return $file->storePublicly($path, $disk);
    }

    public function deleteFile(string $path, string $disk = self::DEFAULT_DISK): bool
    {
        if ($this->fileExists($path, $disk)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public function fileExists(string $path, string $disk = self::DEFAULT_DISK): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function getFileSizeInKB(UploadedFile $file): int
    {
        return round($file->getSize() / 1024);
    }

    public function isFileSizeExceeded(UploadedFile $file, int $maxSizeKB = self::MAX_FILE_SIZE_KB): bool
    {
        return $file->getSize() > $maxSizeKB * 1024;
    }
}
