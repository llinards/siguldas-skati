<?php

namespace App\Livewire\Admin\Rule;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\RuleService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RuleList extends Component
{
    use WithPagination;

    private RuleService $ruleService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        RuleService $ruleService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->ruleService = $ruleService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteRule(int $ruleId): void
    {
        try {
            $rule = $this->ruleService->getRuleById($ruleId);

            if (! $rule) {
                $this->flashMessageService->error(__('Noteikums nav atrasts vai nevarēja tikt dzēsts.'));

                return;
            }

            $this->ruleService->deleteRule($rule);
            $this->flashMessageService->success(__('Noteikums veiksmīgi dzēsts.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete rule', $e, ['rule_id' => $ruleId]);
            $this->flashMessageService->error(__('Dzēšot noteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $ruleId): void
    {
        try {
            if ($this->ruleService->toggleRuleStatus($ruleId)) {
                $this->flashMessageService->success(__('Noteikuma statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Noteikums nav atrasts vai nevarēja tikt atjaunināts.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle rule status', $e, ['rule_id' => $ruleId]);
            $this->flashMessageService->error(__('Atjauninot noteikuma statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateRuleOrder(array $rules): void
    {
        try {
            $this->ruleService->updateRuleOrder($rules);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update rule order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $rules = $this->ruleService->getAllRules();

            return view('livewire.admin.rule.rule-list', compact('rules'))
                ->layout('layouts.admin.app');
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load rules list', $e, []);
            $this->flashMessageService->error(__('Ielādējot noteikumus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.rule.rule-list', [
                'rules' => collect([]),
            ])->layout('layouts.admin.app');
        }
    }
}
