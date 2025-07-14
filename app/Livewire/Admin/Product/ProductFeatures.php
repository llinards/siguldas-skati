<?php

namespace App\Livewire\Admin\Product;

use App\Services\ErrorLogService;
use App\Services\FeatureService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductFeatures extends Component
{
    public $product;

    public array $selectedFeatures;

    private ProductServices $productServices;

    private FeatureService $featureService;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        ProductServices $productServices,
        FeatureService $featureService,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->productServices = $productServices;
        $this->featureService = $featureService;
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

        $this->selectedFeatures = $this->product->features->pluck('id')->toArray();
    }

    public function save(): void
    {
        try {
            $this->productServices->syncProductFeatures($this->product, $this->selectedFeatures);
            $this->flashMessageService->success(__('Mājas ērtības veiksmīgi atjauninātas.'));
            $this->redirect(route('dashboard'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to save product features', $e, [
                'product_id' => $this->product->id,
                'selected_features' => $this->selectedFeatures,
            ]);
            $this->flashMessageService->error(__('Saglabājot funkcijas, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        $features = $this->featureService->getAllActiveFeatures();

        return view('livewire.admin.product.product-features', [
            'features' => $features,
        ])->layout('layouts.admin.app');
    }
}
