<?php

namespace App\Livewire\Admin\Product;

use App\Models\ProductImage;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductGallery extends Component
{
    use WithFileUploads;

    public $product;
    public array $images = [];

    private ProductServices $productServices;
    private const MAX_FILE_SIZE_KB = 512;
    private const MAX_FILE_SIZE_BYTES = self::MAX_FILE_SIZE_KB * 1024;
    private const STORAGE_PATH = 'product-images/gallery';

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);
    }

    public function isImageOversized(int $index): bool
    {
        if ( ! isset($this->images[$index])) {
            return false;
        }

        return $this->images[$index]->getSize() > self::MAX_FILE_SIZE_BYTES;
    }

    public function getImageSizeInKB(int $index): int
    {
        if ( ! isset($this->images[$index])) {
            return 0;
        }

        return round($this->images[$index]->getSize() / 1024);
    }

    public function updatedImages(): void
    {
        $oversizedCount = collect($this->images)
            ->filter(fn($image, $index) => $this->isImageOversized($index))
            ->count();

        if ($oversizedCount > 0) {
            $this->flashError(__('Attēli ar sarkanu apmali pārsniedz :size KB ierobežojumu un netiks saglabāti.', [
                'size' => self::MAX_FILE_SIZE_KB,
            ]));
        } else {
            session()->forget('error');
        }
    }

    public function store(): void
    {
        $this->validateImages();

        try {
            $this->storeImages();
            $this->resetForm();
            $this->flashSuccess(__('Galerija veiksmīgi atjaunināta!'));
        } catch (\Exception $e) {
            $this->logError('Failed to save product gallery.', $e);
            $this->flashError(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeImage(int $imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        $this->deleteImageFile($image->filename);
        $image->delete();

        $this->flashSuccess(__('Attēls dzēsts!'));
    }

    public function removeNewImage(int $index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
        $this->updatedImages();
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-gallery')
            ->layout('layouts.admin.app');
    }

    private function validateImages(): void
    {
        $this->validate([
            'images'   => 'required|array',
            'images.*' => 'image|max:'.self::MAX_FILE_SIZE_KB,
        ], [
            'images.required' => 'Lūdzu, izvēlies vismaz vienu attēlu.',
            'images.*.image'  => 'Katram failam jābūt attēlam.',
            'images.*.max'    => 'Attēls nedrīkst pārsniegt :max KB.',
        ]);
    }

    private function storeImages(): void
    {
        foreach ($this->images as $image) {
            $path = $image->store(self::STORAGE_PATH, 'public');

            ProductImage::create([
                'product_id' => $this->product->id,
                'filename'   => $path,
            ]);
        }
    }

    private function deleteImageFile(string $filename): void
    {
        if (Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }
    }

    private function resetForm(): void
    {
        $this->images = [];
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
        ]);
    }
}
