<?php

namespace App\Livewire\Admin\Product;

use App\Models\BlockedDate;
use App\Services\FlashMessageService;
use App\Services\ProductServices;
use Illuminate\View\View;
use Livewire\Component;

class BlockedDates extends Component
{
    public $product;

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
        return view('livewire.admin.product.blocked-dates', [
            'blocks' => $this->product->blockedDates()->orderBy('start_date')->get(),
        ])->layout('layouts.admin.app');
    }
}
