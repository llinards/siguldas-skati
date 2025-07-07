<?php

namespace App\Livewire\Admin\Product;

use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    #[Validate('nullable')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $cover;

    public bool $is_active = false;

    private ProductServices $productServices;
    private const STORAGE_PATH = 'product-images';

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
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
            $this->flashSuccess(__('Produkts atjaunots.'));
        } catch (\Exception $e) {
            $this->logError('Failed to update product.', $e);
            $this->flashError(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
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
        $this->title       = $this->product->title;
        $this->description = $this->product->description;
        $this->is_active   = (bool) $this->product->is_active;
    }

    private function updateProduct(): void
    {
        $updateData = $this->prepareUpdateData();
        $this->product->update($updateData);
    }

    private function prepareUpdateData(): array
    {
        $updateData = [
            'title'       => $this->title,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'slug'        => $this->generateSlug(),
        ];

        if ($this->hasNewCover()) {
            $this->removeOldCover();
            $updateData['cover'] = $this->storeNewCover();
        }

        return $updateData;
    }

    private function hasNewCover(): bool
    {
        return $this->cover !== null;
    }

    private function removeOldCover(): void
    {
        if ($this->product->cover && $this->coverExistsInStorage()) {
            Storage::disk('public')->delete($this->product->cover);
        }
    }

    private function coverExistsInStorage(): bool
    {
        return Storage::disk('public')->exists($this->product->cover);
    }

    private function storeNewCover(): string
    {
        return $this->cover->storePublicly(self::STORAGE_PATH, 'public');
    }

    private function generateSlug(): string
    {
        return $this->productServices->generateSlug($this->title);
    }

    private function flashSuccess(string $message): void
    {
        session()->flash('message', $message);
    }

    private function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    private function logError(string $message, \Exception $e): void
    {
        Log::error($message, [
            'error'      => $e->getMessage(),
            'product_id' => $this->product->id,
            'title'      => $this->title,
        ]);
    }
}
