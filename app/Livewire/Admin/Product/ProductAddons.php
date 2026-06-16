<?php

namespace App\Livewire\Admin\Product;

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductAddons extends Component
{
    public $product;

    public ?int $editingId = null;

    public string $nameLv = '';

    public string $nameEn = '';

    public string $descLv = '';

    public string $descEn = '';

    public bool $isActive = true;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    public function boot(ProductServices $productServices, FlashMessageService $flashMessageService): void
    {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
    }

    public function mount($product): void
    {
        $this->product = $this->productServices->getProductById($product);

        if (! $this->product) {
            $this->flashMessageService->error(__('Produkts nav atrasts.'));
            $this->redirect(route('dashboard.products'));
        }
    }

    public function save(): void
    {
        $this->validate([
            'nameLv' => 'required|string|max:120',
            'nameEn' => 'nullable|string|max:120',
            'descLv' => 'nullable|string|max:500',
            'descEn' => 'nullable|string|max:500',
        ]);

        $attributes = [
            'name' => ['lv' => $this->nameLv, 'en' => $this->nameEn],
            'description' => ['lv' => $this->descLv, 'en' => $this->descEn],
            'is_active' => $this->isActive,
        ];

        if ($this->editingId !== null) {
            Addon::where('product_id', $this->product->id)->whereKey($this->editingId)->update($attributes);
        } else {
            $this->product->addons()->create([
                ...$attributes,
                'price' => 0,
                'pricing_type' => AddonPricingType::PerStay,
                'order' => (int) $this->product->addons()->max('order') + 1,
            ]);
        }

        $this->resetForm();
        $this->flashMessageService->success(__('Papildinājums saglabāts.'));
    }

    public function edit(int $addonId): void
    {
        $addon = $this->product->addons()->findOrFail($addonId);

        $this->editingId = $addon->id;
        $this->nameLv = $addon->getTranslation('name', 'lv') ?? '';
        $this->nameEn = $addon->getTranslation('name', 'en') ?? '';
        $this->descLv = $addon->getTranslation('description', 'lv') ?? '';
        $this->descEn = $addon->getTranslation('description', 'en') ?? '';
        $this->isActive = $addon->is_active;
    }

    public function toggleActive(int $addonId): void
    {
        $addon = $this->product->addons()->findOrFail($addonId);
        $addon->update(['is_active' => ! $addon->is_active]);
    }

    public function delete(int $addonId): void
    {
        Addon::where('product_id', $this->product->id)->whereKey($addonId)->delete();
        $this->flashMessageService->success(__('Papildinājums dzēsts.'));
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'nameLv', 'nameEn', 'descLv', 'descEn']);
        $this->isActive = true;
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-addons', [
            'addons' => $this->product->addons()->get(),
        ])->layout('layouts.admin.app');
    }
}
