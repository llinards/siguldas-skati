<?php

namespace App\Livewire\Admin\Experience;

use App\Services\ErrorLogService;
use App\Services\ExperienceService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ExperienceList extends Component
{
    use WithPagination;

    private ExperienceService $experienceService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        ExperienceService $experienceService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->experienceService = $experienceService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteExperience(int $experienceId): void
    {
        try {
            $experience = $this->experienceService->getExperienceById($experienceId);

            if (! $experience) {
                $this->flashMessageService->error(__('Pieredze nav atrasta vai nevarēja tikt dzēsta.'));

                return;
            }

            $this->experienceService->deleteExperience($experience);
            $this->flashMessageService->success(__('Pieredze veiksmīgi dzēsta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete experience', $e, ['experience_id' => $experienceId]);
            $this->flashMessageService->error(__('Dzēšot pieredzi, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $experienceId): void
    {
        try {
            if ($this->experienceService->toggleExperienceStatus($experienceId)) {
                $this->flashMessageService->success(__('Pieredzes statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Pieredze nav atrasta vai nevarēja tikt atjaunināta.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle experience status', $e, ['experience_id' => $experienceId]);
            $this->flashMessageService->error(__('Atjauninot pieredzes statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateExperienceOrder(string $id, int $position): void
    {
        try {
            $this->experienceService->updateExperienceOrder((int) $id, $position);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update experience order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $experiences = $this->experienceService->getAllExperiences();

            return view('livewire.admin.experience.experience-list', compact('experiences'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load experiences list', $e, []);
            $this->flashMessageService->error(__('Ielādējot pieredzi, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.experience.experience-list', [
                'experiences' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
