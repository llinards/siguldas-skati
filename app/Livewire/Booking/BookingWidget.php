<?php

namespace App\Livewire\Booking;

use App\Exceptions\BookingException;
use App\Models\Addon;
use App\Models\Product;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Services\StripeService;
use App\Support\BookingQuote;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookingWidget extends Component
{
    public Product $product;

    #[Validate('required|date|after_or_equal:today')]
    public string $checkIn = '';

    #[Validate('required|date|after:checkIn')]
    public string $checkOut = '';

    #[Validate('required|integer|min:1')]
    public int $adults = 2;

    #[Validate('integer|min:0')]
    public int $children = 0;

    /** @var array<int, bool> addon_id => selected */
    public array $selectedAddons = [];

    #[Validate('required|string|min:2|max:120')]
    public string $guestName = '';

    #[Validate('required|email|max:255')]
    public string $guestEmail = '';

    #[Validate('required|string|min:6|max:40')]
    public string $guestPhone = '';

    public ?string $bookingError = null;

    public int $quoteTotal = 0;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function updatedCheckIn(): void
    {
        $this->recomputeQuoteTotal();
    }

    public function updatedCheckOut(): void
    {
        $this->recomputeQuoteTotal();
    }

    public function updatedSelectedAddons(): void
    {
        $this->recomputeQuoteTotal();
    }

    private function recomputeQuoteTotal(): void
    {
        $this->quoteTotal = $this->quote()?->grandTotal ?? 0;
    }

    /**
     * @return array<int, array{addon: Addon, quantity: int}>
     */
    private function addonSelections(): array
    {
        if (empty(array_filter($this->selectedAddons))) {
            return [];
        }

        return $this->product->addons()
            ->where('is_active', true)
            ->whereIn('id', array_keys(array_filter($this->selectedAddons)))
            ->get()
            ->map(fn (Addon $addon) => ['addon' => $addon, 'quantity' => 1])
            ->all();
    }

    private function quote(): ?BookingQuote
    {
        if ($this->checkIn === '' || $this->checkOut === '') {
            return null;
        }

        try {
            return app(PricingService::class)->quote(
                $this->product,
                Carbon::parse($this->checkIn),
                Carbon::parse($this->checkOut),
                $this->addonSelections(),
            );
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public function reserve(BookingService $bookings, StripeService $stripe): mixed
    {
        $this->bookingError = null;
        $this->validate();

        try {
            $booking = $bookings->createPendingBooking(
                $this->product,
                Carbon::parse($this->checkIn),
                Carbon::parse($this->checkOut),
                $this->adults,
                $this->children,
                $this->addonSelections(),
                ['name' => $this->guestName, 'email' => $this->guestEmail, 'phone' => $this->guestPhone],
            );
        } catch (BookingException $e) {
            $this->bookingError = $e->getMessage();

            return null;
        }

        $session = $stripe->createCheckoutSession(
            $booking,
            route('booking.success', $booking->reference).'?session_id={CHECKOUT_SESSION_ID}',
            route('booking.cancel', $booking->reference),
        );

        $booking->update(['stripe_session_id' => $session->id]);

        return $this->redirect($session->url);
    }

    public function render(): View
    {
        return view('livewire.booking.booking-widget', [
            'quote' => $this->quote(),
            'addons' => $this->product->addons()->where('is_active', true)->get(),
        ]);
    }
}
