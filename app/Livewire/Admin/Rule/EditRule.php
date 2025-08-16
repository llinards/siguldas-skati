<?php

namespace App\Livewire\Admin\Rule;

use App\Models\Rule as RuleModel;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\RuleService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditRule extends Component
{
    use WithFileUploads;

    public RuleModel $rule;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $section = RuleModel::SECTION_HOUSE;

    #[Validate('nullable')]
    #[Validate('max:10', message: 'Bildes izmērs nedrīkst pārsniegt 10 kb.')]
    #[Validate('mimes:svg,png', message: 'Drīkst pievienot tikai SVG vai PNG formāta ikonas.')]
    public $icon_image;

    public bool $is_active = true;

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

    public function mount(RuleModel $rule): void
    {
        $this->rule = $rule;
        $this->title = $this->rule->title;
        $this->section = $this->rule->section;
        $this->is_active = $this->rule->is_active;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $this->updateRule();
            $this->flashMessageService->success(__('Noteikums veiksmīgi atjaunināts.'));
            $this->redirect(route('dashboard.rules'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update rule', $e, ['rule_id' => $this->rule->id]);
            $this->flashMessageService->error(__('Saglabājot noteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function updateRule(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->ruleService->updateRule($this->rule, $updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title' => $this->title,
            'section' => $this->section,
            'is_active' => $this->is_active,
        ];

        if ($this->hasNewIcon()) {
            $updateData['icon_image'] = $this->ruleService->updateRuleIcon($this->rule, $this->icon_image);
        }

        return $updateData;
    }

    private function hasNewIcon(): bool
    {
        return $this->icon_image !== null;
    }

    public function render(): View
    {
        return view('livewire.admin.rule.edit-rule')
            ->layout('layouts.admin.app');
    }
}
