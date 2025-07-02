<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddProduct extends Component
{
    #[Validate('required', message: 'validation.required')]
    public string $title;

    #[Validate('required', message: 'validation.required')]
    public string $description;

    public bool $is_active;

    public string $slug;

    protected ProductServices $productServices;

    public function store(): void
    {
        $this->validate();
        try {
            $this->slug = $this->productServices->generateSlug($this->title);

            Product::create(array_merge(
                $this->only(['title', 'description', 'is_active']),
                ['cover' => 'storage/product-images/siguldas-skati-product-1.jpg'],
                ['slug' => $this->slug]
            ));
            session()->flash('message', __('Produkts pievienots.'));
        } catch (\Exception $e) {
            Log::error('Failed to store product.', [
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function render()
    {
        return view('livewire.admin.product.add-product')->layout('layouts.admin.app');
    }
}
