<?php

namespace App\Livewire\Admin\Feature;

use App\Services\ErrorLogService;
use App\Services\FeatureService;
use App\Services\FlashMessageService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FeatureList extends Component
{
    use WithPagination;

    private FeatureService $featureService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        FeatureService $featureService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->featureService = $featureService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteFeature(int $featureId): void
    {
        try {
            $feature = $this->featureService->getFeatureById($featureId);

            if (! $feature) {
                $this->flashMessageService->error(__('Ērtība nav atrasta vai nevarēja tikt dzēsta.'));

                return;
            }

            $this->featureService->deleteFeature($feature);
            $this->flashMessageService->success(__('Ērtība veiksmīgi dzēsta.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete feature', $e, ['feature_id' => $featureId]);
            $this->flashMessageService->error(__('Dzēšot ērtību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $featureId): void
    {
        try {
            if ($this->featureService->toggleFeatureStatus($featureId)) {
                $this->flashMessageService->success(__('Ērtības statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Ērtība nav atrasta vai nevarēja tikt atjaunināta.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle feature status', $e, ['feature_id' => $featureId]);
            $this->flashMessageService->error(__('Atjauninot ērtības statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateFeatureOrder(array $features): void
    {
        try {
            $this->featureService->updateFeatureOrder($features);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update feature order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $features = $this->featureService->getAllFeatures();

            return view('livewire.admin.feature.feature-list', compact('features'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load features list', $e, []);
            $this->flashMessageService->error(__('Ielādējot ērtību, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.feature.feature-list', [
                'features' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
