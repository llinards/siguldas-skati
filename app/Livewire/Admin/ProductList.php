<?php

namespace App\Livewire\Admin;

use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected ProductServices $productServices;

    public function boot(ProductServices $productServices)
    {
        $this->productServices = $productServices;
    }

    public function deleteProduct($productId): void
    {
        try {
            if ($this->productServices->deleteProduct($productId)) {
                session()->flash('message', __('Produkts veiksmīgi dzēsts.'));
            } else {
                session()->flash('error', __('Produkts nav atrasts vai nevarēja tikt dzēsts.'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'product_id' => $productId,
                'error'      => $e->getMessage(),
            ]);
            session()->flash('error', __('Dzēšot produktu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive($productId): void
    {
        try {
            if ($this->productServices->toggleProductStatus($productId)) {
                session()->flash('message', __('Produkta statuss veiksmīgi atjaunināts.'));
            } else {
                session()->flash('error', __('Produkts nav atrasts vai nevarēja tikt atjaunināts.'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', [
                'product_id' => $productId,
                'error'      => $e->getMessage(),
            ]);
            session()->flash('error', __('Atjauninot produkta statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $products = $this->productServices->getAllProducts(12);

            return view('livewire.admin.product-list', compact('products'));
        } catch (\Exception $e) {
            Log::error('Failed to load products list', [
                'error' => $e->getMessage(),
            ]);

            // Return empty collection to prevent breaking the page
            $products = collect()->paginate(12);
            session()->flash('error', __('Ielādējot produktus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.product-list', compact('products'));
        }
    }
}
