<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ActivityService
{
    private FileStorageService $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function getAllActivities(): Collection
    {
        return Activity::all();
    }

    public function getAllActiveActivities(): Collection
    {
        return Activity::where('is_active', true)->get();
    }

    public function getActivityById(int $id): ?Activity
    {
        return Activity::find($id);
    }

    public function createActivity(array $activityData): Activity
    {
        return Activity::create($activityData);
    }

    public function updateActivity(Activity $activity, array $activityData): bool
    {
        return $activity->update($activityData);
    }

    public function deleteActivity(Activity $activity): bool
    {
        if ($activity->image) {
            $this->fileStorageService->deleteFile($activity->image);
        }

        return $activity->delete();
    }

    public function toggleActivityStatus(int $activityId): bool
    {
        $activity = Activity::find($activityId);

        if (! $activity) {
            return false;
        }

        return $activity->update(['is_active' => ! $activity->is_active]);
    }

    public function updateActivityOrder(int $id, int $position): void
    {
        $activity = Activity::findOrFail($id);
        $activities = Activity::orderBy('order')->get()->reject(fn ($a) => $a->id === $activity->id)->values();
        $activities->splice($position, 0, [$activity]);

        foreach ($activities as $index => $a) {
            if ($a->order !== $index) {
                $a->update(['order' => $index]);
            }
        }
    }

    public function storeActivityImage(UploadedFile $file): string
    {
        return $this->fileStorageService->storeFile(
            $file,
            FileStorageService::ACTIVITY_IMAGE_PATH
        );
    }

    public function updateActivityImage(Activity $activity, UploadedFile $file): string
    {
        if ($activity->image) {
            $this->fileStorageService->deleteFile($activity->image);
        }

        return $this->storeActivityImage($file);
    }
}
