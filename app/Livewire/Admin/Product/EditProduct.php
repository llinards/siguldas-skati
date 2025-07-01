<?php

namespace App\Livewire\Admin\Product;

use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditProduct extends Component
{
  public $product;

  #[Validate('required', message: 'validation.required')]
  public string $title;

  #[Validate('required', message: 'validation.required')]
  public string $description;

  public bool $is_active;

  public string $slug;

  protected ProductServices $productServices;

  public function update(): void
  {
    $this->validate();
    try {
      $this->slug = $this->productServices->generateSlug($this->title);

      $this->product->update(array_merge(
          $this->only(['title', 'description', 'is_active']),
          ['slug' => $this->slug]
      ));

      session()->flash('message', __('Produkts atjaunots.'));
    } catch (\Exception $e) {
      Log::error('Failed to update product.', [
          'product_id' => $this->product->id,
          'error'      => $e->getMessage(),
      ]);
      session()->flash('error', __('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
    }
  }

  public function boot(ProductServices $productServices): void
  {
    $this->productServices = $productServices;
  }

  public function mount($product): void
  {
    $this->product     = $this->productServices->getProductById($product);
    $this->title       = $this->product->title;
    $this->description = $this->product->description;
    $this->is_active   = (bool) $this->product->is_active;
  }

  public function render(): View
  {
    return view('livewire.admin.product.edit-product')->layout('layouts.admin.app');
  }
}
