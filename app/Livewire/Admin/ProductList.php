<?php

namespace App\Livewire\Admin;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    public function boot(
        ProductServices $productServices,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function deleteProduct(int $productId): void
    {
        try {
            $product = $this->productServices->getProductById($productId);

            if (! $product) {
                $this->flashMessageService->error(__('Produkts nav atrasts vai nevarēja tikt dzēsts.'));

                return;
            }

            $this->productServices->deleteProduct($product);
            $this->flashMessageService->success(__('Produkts veiksmīgi dzēsts.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to delete product', $e, ['product_id' => $productId]);
            $this->flashMessageService->error(__('Dzēšot produktu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function toggleActive(int $productId): void
    {
        try {
            if ($this->productServices->toggleProductStatus($productId)) {
                $this->flashMessageService->success(__('Produkta statuss veiksmīgi atjaunināts.'));
            } else {
                $this->flashMessageService->error(__('Produkts nav atrasts vai nevarēja tikt atjaunināts.'));
            }
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to toggle product status', $e, ['product_id' => $productId]);
            $this->flashMessageService->error(__('Atjauninot produkta statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function updateProductOrder(array $products): void
    {
        try {
            $this->productServices->updateProductOrder($products);
            $this->flashMessageService->success(__('Secība atjaunota.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update product order', $e, []);
            $this->flashMessageService->error(__('Atjauninot secību, radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        try {
            $products = $this->productServices->getAllProducts();

            return view('livewire.admin.product-list', compact('products'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to load products list', $e, []);
            $this->flashMessageService->error(__('Ielādējot produktus, radās kļūda. Lūdzu, atsvaidziniet lapu.'));

            return view('livewire.admin.product-list', [
                'products' => collect([]),
            ]);
        }
    }
}
