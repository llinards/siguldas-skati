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

class AddRule extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $section = RuleModel::SECTION_HOUSE;

    #[Validate('required', message: 'validation.required')]
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

    public function save(): void
    {
        $this->validate();

        try {
            $this->createRule();
            $this->flashMessageService->success(__('Noteikums veiksmīgi izveidots.'));
            $this->redirect(route('dashboard.rules'));

        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to create rule', $e, []);
            $this->flashMessageService->error(__('Veidojot noteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    private function createRule(): void
    {
        $ruleData = $this->prepareRuleData();
        $this->ruleService->createRule($ruleData);
    }

    private function prepareRuleData(): array
    {
        $icon = null;
        if ($this->icon_image) {
            $icon = $this->ruleService->storeRuleIcon($this->icon_image);
        }

        return [
            'title' => $this->title,
            'section' => $this->section,
            'is_active' => $this->is_active,
            'order' => $this->ruleService->getAllRules()->count() + 1,
            'icon_image' => $icon,
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.rule.add-rule')
            ->layout('layouts.admin.app');
    }
}
