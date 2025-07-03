<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected ProductServices $productServices;

    protected Product $product;

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function deleteProduct($productId): void
    {
        try {
            $this->product = $this->productServices->getProductById($productId);
            if (Storage::disk('public')->exists($this->product->cover)) {
                Storage::disk('public')->delete($this->product->cover);
            }
            if ($this->productServices->deleteProduct($this->product)) {
                session()->flash('message', __('Produkts veiksmīgi dzēsts.'));
            } else {
                session()->flash('error', __('Produkts nav atrasts vai nevarēja tikt dzēsts.'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'product_id' => $this->product->id,
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


    public function updateProductOrder(array $products): void
    {
        foreach ($products as $product) {
            Product::findOrFail($product['value'])->update(['order' => $product['order']]);
        }

        session()->flash('message', 'Secība atjaunota.');
    }

    public function render(): View
    {
        try {
            $products = $this->productServices->getAllProducts();

            return view('livewire.admin.product-list', compact('products'));
        } catch (\Exception $e) {
            Log::error('Failed to load products list', [
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('Ielādējot produktus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.product-list', compact('products'));
        }
    }
}
