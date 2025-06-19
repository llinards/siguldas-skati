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
                session()->flash('message', 'Product deleted successfully.');
            } else {
                session()->flash('error', 'Product not found or could not be deleted.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'product_id' => $productId,
                'error'      => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while deleting the product. Please try again.');
        }
    }

    public function toggleActive($productId): void
    {
        try {
            if ($this->productServices->toggleProductStatus($productId)) {
                session()->flash('message', 'Product status updated successfully.');
            } else {
                session()->flash('error', 'Product not found or could not be updated.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', [
                'product_id' => $productId,
                'error'      => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while updating the product status. Please try again.');
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
            session()->flash('error', 'An error occurred while loading products. Please refresh the page.');

            return view('livewire.admin.product-list', compact('products'));
        }
    }
}
