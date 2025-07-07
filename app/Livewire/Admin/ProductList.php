<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    private ProductServices $productServices;

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function deleteProduct(int $productId): void
    {
        try {
            $product = $this->findProduct($productId);

            if ( ! $product) {
                $this->flashError(__('Produkts nav atrasts vai nevarēja tikt dzēsts.'));

                return;
            }

            $this->deleteProductWithCover($product);
            $this->flashSuccess(__('Produkts veiksmīgi dzēsts.'));
        } catch (\Exception $e) {
            $this->logError('Failed to delete product', $e, $productId);
            $this->flashError(__('Dzēšot produktu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $productId): void
    {
        try {
            if ($this->productServices->toggleProductStatus($productId)) {
                $this->flashSuccess(__('Produkta statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashError(__('Produkts nav atrasts vai nevarēja tikt atjaunināts.'));
            }
        } catch (\Exception $e) {
            $this->logError('Failed to toggle product status', $e, $productId);
            $this->flashError(__('Atjauninot produkta statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateProductOrder(array $products): void
    {
        try {
            $this->processOrderUpdate($products);
            $this->flashSuccess(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->logError('Failed to update product order', $e);
            $this->flashError(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $products = $this->loadProducts();

            return view('livewire.admin.product-list', compact('products'));
        } catch (\Exception $e) {
            $this->logError('Failed to load products list', $e);
            $this->flashError(__('Ielādējot produktus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.product-list', [
                'products' => collect([]),
            ]);
        }
    }

    private function findProduct(int $productId): ?Product
    {
        return $this->productServices->getProductById($productId);
    }

    private function deleteProductWithCover(Product $product): void
    {
        $this->deleteCoverImage($product);
        $this->deleteProductRecord($product);
    }

    private function deleteCoverImage(Product $product): void
    {
        if ($product->cover && $this->coverExists($product->cover)) {
            Storage::disk('public')->delete($product->cover);
        }
    }

    private function coverExists(string $coverPath): bool
    {
        return Storage::disk('public')->exists($coverPath);
    }

    private function deleteProductRecord(Product $product): bool
    {
        return $this->productServices->deleteProduct($product);
    }

    private function processOrderUpdate(array $products): void
    {
        foreach ($products as $productData) {
            $this->updateSingleProductOrder($productData);
        }
    }

    private function updateSingleProductOrder(array $productData): void
    {
        Product::findOrFail($productData['value'])
               ->update(['order' => $productData['order']]);
    }

    private function loadProducts(): Collection
    {
        return $this->productServices->getAllProducts();
    }

    private function flashSuccess(string $message): void
    {
        session()->flash('message', $message);
    }

    private function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    private function logError(string $message, \Exception $e, ?int $productId = null): void
    {
        $context = [
            'error' => $e->getMessage(),
        ];

        if ($productId) {
            $context['product_id'] = $productId;
        }

        Log::error($message, $context);
    }
}
