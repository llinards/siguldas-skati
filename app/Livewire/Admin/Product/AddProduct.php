<?php

namespace App\Livewire\Admin\Product;

use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
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
    public int $personCount;

    #[Validate('required', message: 'validation.required')]
    public string $pricelist = '';

    #[Validate('required', message: 'validation.required')]
    #[Validate('max:512', message: 'Bildes izmērs nedrīkst pārsniegt 512 kb.')]
    #[Validate('image', message: 'Drīkst pievienot tikai vienu bildi.')]
    public $cover;

    public bool $is_active = false;

    private ProductServices $productServices;

    private FlashMessageService $flashMessageService;

    private ErrorLogService $errorLogService;

    private const SUCCESS_ROUTE = 'dashboard';

    public function boot(
        ProductServices $productServices,
        FlashMessageService $flashMessageService,
        ErrorLogService $errorLogService
    ): void {
        $this->productServices = $productServices;
        $this->flashMessageService = $flashMessageService;
        $this->errorLogService = $errorLogService;
    }

    public function store(): void
    {
        $this->validate();

        try {
            $this->createProduct();
            $this->flashMessageService->success(__('Produkts pievienots.'));
            $this->redirectToSuccess();
        } catch (\Exception $e) {
            $this->errorLogService->logError('Failed to store product.', $e, ['title' => $this->title]);
            $this->flashMessageService->error(__('Radās kļūda. Lūdzu, mēģiniet vēlreiz.'));
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
        $this->productServices->createProduct($productData);
    }

    private function prepareProductData(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'person_count' => $this->personCount,
            'pricelist' => $this->pricelist,
            'cover' => $this->productServices->storeProductCover($this->cover),
            'slug' => $this->productServices->generateSlug($this->title),
        ];
    }

    private function redirectToSuccess(): void
    {
        $this->redirect(route(self::SUCCESS_ROUTE));
    }
}
