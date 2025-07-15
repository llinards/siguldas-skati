<?php

namespace App\Services;

use App\Models\Feature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class FeatureService
{
    private FileStorageService $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function getAllFeatures(): Collection
    {
        return Feature::all();
    }

    public function getAllActiveFeatures(): Collection
    {
        return Feature::where('is_active', true)->get();
    }

    public function getFeatureById(int $id): ?Feature
    {
        return Feature::find($id);
    }

    public function createFeature(array $featureData): Feature
    {
        return Feature::create($featureData);
    }

    public function updateFeature(Feature $feature, array $featureData): bool
    {
        return $feature->update($featureData);
    }

    public function deleteFeature(Feature $feature): bool
    {
        if ($feature->icon_image) {
            $this->fileStorageService->deleteFile($feature->icon_image);
        }

        return $feature->delete();
    }

    public function toggleFeatureStatus(int $featureId): bool
    {
        $feature = Feature::find($featureId);

        if (! $feature) {
            return false;
        }

        return $feature->update(['is_active' => ! $feature->is_active]);
    }

    public function updateFeatureOrder(array $features): void
    {
        foreach ($features as $featureData) {
            Feature::findOrFail($featureData['value'])
                ->update(['order' => $featureData['order']]);
        }
    }

    public function storeFeatureIcon(UploadedFile $file): string
    {
        return $this->fileStorageService->storeFile(
            $file,
            FileStorageService::FEATURE_ICON_PATH
        );
    }

    public function updateFeatureIcon(Feature $feature, UploadedFile $file): string
    {
        if ($feature->icon_image) {
            $this->fileStorageService->deleteFile($feature->icon_image);
        }

        return $this->storeFeatureIcon($file);
    }
}
