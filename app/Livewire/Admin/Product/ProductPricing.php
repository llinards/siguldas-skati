<?php

namespace App\Livewire\Admin\Product;

use App\Models\BlockedDate;
use App\Models\ProductPrice;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class ProductPricing extends Component
{
    public $product;

    public ?float $basePrice = null;

    public int $minNights = 1;

    /** @var array<int, string> 'Y-m-d' dates selected in the calendar */
    public array $selectedDates = [];

    public ?float $overridePrice = null;

    public string $startDate = '';

    public string $endDate = '';

    public ?string $reason = null;

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

            return;
        }

        $this->basePrice = $this->product->base_price / 100;
        $this->minNights = $this->product->min_nights;
    }

    public function saveBaseSettings(): void
    {
        $this->validate([
            'basePrice' => 'required|numeric|min:0',
            'minNights' => 'required|integer|min:1',
        ]);

        $this->product->update([
            'base_price' => (int) round($this->basePrice * 100),
            'min_nights' => $this->minNights,
        ]);

        $this->flashMessageService->success(__('Cenas iestatījumi saglabāti.'));
    }

    public function applyPriceToSelected(): void
    {
        $this->validate([
            'selectedDates' => 'required|array|min:1',
            'selectedDates.*' => 'date',
            'overridePrice' => 'required|numeric|min:0.01',
        ]);

        $cents = (int) round($this->overridePrice * 100);

        foreach ($this->selectedDates as $date) {
            ProductPrice::updateOrCreate(
                ['product_id' => $this->product->id, 'date' => $date],
                ['price' => $cents],
            );
        }

        $this->selectedDates = [];
        $this->overridePrice = null;

        $this->flashMessageService->success(__('Cenas atjauninātas izvēlētajiem datumiem.'));
    }

    public function removeOverride(int $priceId): void
    {
        ProductPrice::where('product_id', $this->product->id)->whereKey($priceId)->delete();

        $this->flashMessageService->success(__('Cenas korekcija dzēsta.'));
    }

    public function addBlock(): void
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'nullable|string|max:255',
        ]);

        $this->product->blockedDates()->create([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'reason' => $this->reason,
        ]);

        $this->reset(['startDate', 'endDate', 'reason']);
        $this->flashMessageService->success(__('Datumi bloķēti.'));
    }

    public function removeBlock(int $blockId): void
    {
        BlockedDate::where('product_id', $this->product->id)->whereKey($blockId)->delete();

        $this->flashMessageService->success(__('Bloķējums noņemts.'));
    }

    public function render(): View
    {
        return view('livewire.admin.product.product-pricing', [
            'overrides' => $this->product->prices()->orderBy('date')->get(),
            'blocks' => $this->product->blockedDates()->orderBy('start_date')->get(),
        ])->layout('layouts.admin.app');
    }
}
