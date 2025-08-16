<?php

namespace App\Livewire\Admin\Product;

use App\Models\Rule;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use App\Services\RuleService;
use Illuminate\View\View;
use Livewire\Component;

class ProductRules extends Component
{
    public $product;

    public array $selectedRules;

    private ProductServices $productServices;

    private RuleService $ruleService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        ProductServices $productServices,
        RuleService $ruleService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->productServices = $productServices;
        $this->ruleService = $ruleService;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);
        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard'));

            return;
        }

        $this->selectedRules = $this->product->rules->pluck('id')->toArray();
    }

    public function save(): void
    {
        try {
            $this->productServices->syncProductRules($this->product, $this->selectedRules);
            $this->flashMessageService->success(__('Mājas noteikumi veiksmīgi atjaunināti.'));
            $this->redirect(route('dashboard'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save product rules', $e, [
                'product_id' => $this->product->id,
                'selected_rules' => $this->selectedRules,
            ]);
            $this->flashMessageService->error(__('Saglabājot noteikumus, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        $rules = $this->ruleService->getAllActiveRules();
        $houseRules = $rules->where('section', Rule::SECTION_HOUSE);
        $safetyRules = $rules->where('section', Rule::SECTION_SAFETY);

        return view('livewire.admin.product.product-rules', [
            'houseRules' => $houseRules,
            'safetyRules' => $safetyRules,
        ])->layout('layouts.admin.app');
    }
}
