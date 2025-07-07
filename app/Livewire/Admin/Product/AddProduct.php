<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddProduct extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'validation.required')]
    public string $title = '';

    #[Validate('required', message: 'validation.required')]
    public string $description = '';

    #[Validate('required', message: 'validation.required')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $cover;

    public bool $is_active = false;

    private ProductServices $productServices;
    private const STORAGE_PATH = 'product-images';
    private const SUCCESS_ROUTE = 'dashboard';

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
    }

    public function store(): void
    {
        $this->validate();

        try {
            $this->createProduct();
            $this->flashSuccess(__('Produkts pievienots.'));
            $this->redirectToSuccess();
        } catch (\Exception $e) {
            $this->logError('Failed to store product.', $e);
            $this->flashError(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
        }
    }

    public function render(): View
    {
        return view('livewire.admin.product.add-product')
            ->layout('layouts.admin.app');
    }

    private function createProduct(): void
    {
        $productData = $this->prepareProductData();
        Product::create($productData);
    }

    private function prepareProductData(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'cover'       => $this->storeCoverImage(),
            'slug'        => $this->generateSlug(),
        ];
    }

    private function storeCoverImage(): string
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
            'error' => $e->getMessage(),
            'title' => $this->title,
        ]);
    }

    private function redirectToSuccess(): void
    {
        $this->redirect(route(self::SUCCESS_ROUTE));
    }
}
