<?php

namespace App\Livewire\Admin\Activity;

use App\Services\ActivityService;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination;

    private ActivityService $activityService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        ActivityService $activityService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->activityService = $activityService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteActivity(int $activityId): void
    {
        try {
            $activity = $this->activityService->getActivityById($activityId);

            if (! $activity) {
                $this->flashMessageService->error(__('Aktivitāte nav atrasta vai nevarēja tikt dzēsta.'));

                return;
            }

            $this->activityService->deleteActivity($activity);
            $this->flashMessageService->success(__('Aktivitāte veiksmīgi dzēsta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete activity', $e, ['activity_id' => $activityId]);
            $this->flashMessageService->error(__('Dzēšot aktivitāti, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $activityId): void
    {
        try {
            if ($this->activityService->toggleActivityStatus($activityId)) {
                $this->flashMessageService->success(__('Aktivitātes statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Aktivitāte nav atrasta vai nevarēja tikt atjaunināta.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle activity status', $e, ['activity_id' => $activityId]);
            $this->flashMessageService->error(__('Atjauninot aktivitātes statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateActivityOrder(string $id, int $position): void
    {
        try {
            $this->activityService->updateActivityOrder((int) $id, $position);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update activity order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $activities = $this->activityService->getAllActivities();

            return view('livewire.admin.activity.activity-list', compact('activities'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load activities list', $e, []);
            $this->flashMessageService->error(__('Ielādējot aktivitātes, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.activity.activity-list', [
                'activities' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
