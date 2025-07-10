<?php

namespace App\Livewire\Admin\Product;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProduct extends Component
{
    use WithFileUploads;

    public $product;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $description = '';

    #[Validate('required', message: 'validation.required')]
    public int $personCount = 0;

    #[Validate('required', message: 'validation.required')]
    public string $pricelist = '';

    #[Validate('nullable')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $cover;

    public bool $is_active = false;

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

    public function mount($product): void
    {
        $this->loadProduct($product);
        $this->initializeFormData();
    }

    public function update(): void
    {
        $this->validate();

        try {
            $this->updateProduct();
            $this->flashMessageService->success(__('Produkts atjaunots.'));
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to update product.', $e, [
                'product_id' => $this->product->id,
                'title' => $this->title,
            ]);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.product.edit-product')
            ->layout('layouts.admin.app');
    }

    private function loadProduct($productId): void
    {
        $this->product = $this->productServices->getProductById($productId);
    }

    private function initializeFormData(): void
    {
        $this->title = $this->product->title;
        $this->description = $this->product->description;
        $this->is_active = (bool) $this->product->is_active;
        $this->personCount = $this->product->person_count;
        $this->pricelist = $this->product->pricelist;
    }

    private function updateProduct(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->productServices->updateProduct($this->product, $updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'person_count' => $this->personCount,
            'pricelist' => $this->pricelist,
            'slug' => $this->productServices->generateSlug($this->title),
        ];

        if ($this->hasNewCover()) {
            $updateData['cover'] = $this->productServices->updateProductCover($this->product, $this->cover);
        }

        return $updateData;
    }

    private function hasNewCover(): bool
    {
        return $this->cover !== null;
    }
}
