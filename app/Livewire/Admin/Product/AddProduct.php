<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddProduct extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title;

    #[Validate('required', message: 'validation.required')]
    public string $description;

    #[Validate('required', message: 'validation.required')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $cover;

    public bool $is_active;

    public string $slug;

    protected ProductServices $productServices;

    public function store(): void
    {
        $this->validate();
        try {
            $this->slug = $this->productServices->generateSlug($this->title);
            $path       = $this->cover->storePublicly('product-images', 'public');

            Product::create(array_merge(
                $this->only(['title', 'description', 'is_active']),
                ['cover' => 'storage/'.$path],
                ['slug' => $this->slug]
            ));
            session()->flash('message', __('Produkts pievienots.'));
            $this->redirect(route('dashboard'));
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
