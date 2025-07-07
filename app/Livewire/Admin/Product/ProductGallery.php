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

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);
    }

    public function isImageOversized($index): bool
    {
        if ( ! isset($this->images[$index])) {
            return false;
        }

        $image          = $this->images[$index];
        $maxSizeInBytes = 512 * 1024;

        return $image->getSize() > $maxSizeInBytes;
    }

    public function getImageSizeInKB($index): int
    {
        if ( ! isset($this->images[$index])) {
            return 0;
        }

        return round($this->images[$index]->getSize() / 1024);
    }

    public function updatedImages(): void
    {
        $oversizedCount = 0;
        foreach ($this->images as $index => $image) {
            if ($this->isImageOversized($index)) {
                $oversizedCount++;
            }
        }

        if ($oversizedCount > 0) {
            session()->flash('error',
                __('Attēli ar sarkanu apmali pārsniedz 512 KB ierobežojumu un netiks saglabāti.'));
        } else {
            session()->forget('error');
        }
    }

    public function store(): void
    {
        $this->validate([
            'images'   => 'required|array',
            'images.*' => 'image|max:512',
        ], [
            'images.required' => 'Lūdzu, izvēlies vismaz vienu attēlu.',
            'images.*.image'  => 'Katram failam jābūt attēlam.',
            'images.*.max'    => 'Attēls nedrīkst pārsniegt 512 KB.',
        ]);

        try {
            foreach ($this->images as $image) {
                $path = $image->store('product-images/gallery', 'public');

                ProductImage::create([
                    'product_id' => $this->product->id,
                    'filename'   => $path,
                ]);
            }
            $this->images = [];
            session()->flash('message', __('Galerija veiksmīgi atjaunināta!'));
        } catch (\Exception $e) {
            Log::error('Failed to save product gallery.', [
                'error'      => $e->getMessage(),
                'product_id' => $this->product->id,
            ]);
            session()->flash('error', __('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function removeImage($imageId): void
    {
        $image = ProductImage::findOrFail($imageId);

        if (Storage::disk('public')->exists($image->filename)) {
            Storage::disk('public')->delete($image->filename);
        }
        $image->delete();
        session()->flash('message', __('Attēls dzēsts!'));
    }

    public function removeNewImage($index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);

        // Re-check for oversized images after removal
        $this->updatedImages();
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-gallery')->layout('layouts.admin.app');
    }
}
