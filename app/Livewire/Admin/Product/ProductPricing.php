<?php

namespace App\Livewire\Admin\Product;

use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductPricing extends Component
{
    public $product;

    public ?float $basePrice = null;

    public int $minNights = 1;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);

        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));

            return;
        }

        $this->basePrice = $this->product->base_price / 100;
        $this->minNights = $this->product->min_nights;
    }

    public function saveBaseSettings(): void
    {
        $this->validate([
            'basePrice' => 'required|numeric|min:0',
            'minNights' => 'required|integer|min:1',
        ]);

        $this->product->update([
            'base_price' => (int) round($this->basePrice * 100),
            'min_nights' => $this->minNights,
        ]);

        $this->flashMessageService->success(__('Cenas iestatījumi saglabāti.'));
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-pricing')
            ->layout('layouts.admin.app');
    }
}
