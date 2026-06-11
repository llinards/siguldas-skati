<?php

namespace App\Services;

use App\Models\Experience;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ExperienceService
{
    private FileStorageService $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function getAllExperiences(): Collection
    {
        return Experience::all();
    }

    public function getAllActiveExperiences(): Collection
    {
        return Experience::where('is_active', true)->get();
    }

    public function getExperienceById(int $id): ?Experience
    {
        return Experience::find($id);
    }

    public function createExperience(array $experienceData): Experience
    {
        return Experience::create($experienceData);
    }

    public function updateExperience(Experience $experience, array $experienceData): bool
    {
        return $experience->update($experienceData);
    }

    public function deleteExperience(Experience $experience): bool
    {
        if ($experience->icon_image) {
            $this->fileStorageService->deleteFile($experience->icon_image);
        }

        return $experience->delete();
    }

    public function toggleExperienceStatus(int $experienceId): bool
    {
        $experience = Experience::find($experienceId);

        if (! $experience) {
            return false;
        }

        return $experience->update(['is_active' => ! $experience->is_active]);
    }

    public function updateExperienceOrder(int $id, int $position): void
    {
        $experience = Experience::findOrFail($id);
        $experiences = Experience::orderBy('order')->get()->reject(fn ($e) => $e->id === $experience->id)->values();
        $experiences->splice($position, 0, [$experience]);

        foreach ($experiences as $index => $e) {
            if ($e->order !== $index) {
                $e->update(['order' => $index]);
            }
        }
    }

    public function storeExperienceIcon(UploadedFile $file): string
    {
        return $this->fileStorageService->storeFile(
            $file,
            FileStorageService::EXPERIENCE_ICON_PATH
        );
    }

    public function updateExperienceIcon(Experience $experience, UploadedFile $file): string
    {
        if ($experience->icon_image) {
            $this->fileStorageService->deleteFile($experience->icon_image);
        }

        return $this->storeExperienceIcon($file);
    }
}
