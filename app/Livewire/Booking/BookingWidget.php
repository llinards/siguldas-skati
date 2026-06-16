<?php

namespace App\Livewire\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Addon;
use App\Models\Product;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Services\StripeService;
use App\Support\BookingQuote;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

    public int $adults = 2;

    public int $children = 0;

    public ?string $guestError = null;

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
        $this->adults = max(1, min($this->adults, $product->person_count));
        $childLimit = $product->children_count > 0 ? $product->children_count : $product->person_count;
        $this->children = max(0, min($this->children, $childLimit, $product->person_count - $this->adults));
    }

    public function incrementAdults(): void
    {
        if ($this->adults + $this->children >= $this->product->person_count) {
            $this->guestError = __('Šī māja paredzēta līdz :count viesiem kopā.', ['count' => $this->product->person_count]);

            return;
        }

        $this->adults++;
        $this->guestError = null;
    }

    public function decrementAdults(): void
    {
        $this->adults = max(1, $this->adults - 1);
        $this->guestError = null;
    }

    public function incrementChildren(): void
    {
        if ($this->product->children_count > 0 && $this->children >= $this->product->children_count) {
            $this->guestError = __('Šī māja paredzēta līdz :count bērniem.', ['count' => $this->product->children_count]);

            return;
        }

        if ($this->adults + $this->children >= $this->product->person_count) {
            $this->guestError = __('Šī māja paredzēta līdz :count viesiem kopā.', ['count' => $this->product->person_count]);

            return;
        }

        $this->children++;
        $this->guestError = null;
    }

    public function decrementChildren(): void
    {
        $this->children = max(0, $this->children - 1);
        $this->guestError = null;
    }

    /**
     * Sync the date range chosen in the JS calendar into Livewire state.
     */
    public function selectDates(?string $checkIn, ?string $checkOut): void
    {
        $this->checkIn = $checkIn ?? '';
        $this->checkOut = $checkOut ?? '';
        $this->recomputeQuoteTotal();
    }

    public function updatedCheckIn(): void
    {
        $this->recomputeQuoteTotal();
    }

    public function updatedCheckOut(): void
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

    /**
     * Nights that cannot be booked (occupied by a confirmed/live-pending booking
     * or an admin-blocked range), as 'Y-m-d' strings, bounded to an 18-month horizon.
     *
     * @return array<int, string>
     */
    private function unavailableDates(): array
    {
        $today = Carbon::today();
        $horizon = $today->copy()->addMonths(18);
        $dates = [];

        $bookings = $this->product->bookings()
            ->where(function ($query) {
                $query->where('status', BookingStatus::Confirmed)
                    ->orWhere(function ($pending) {
                        $pending->where('status', BookingStatus::Pending)
                            ->where('expires_at', '>', now());
                    });
            })
            ->where('check_out', '>', $today->toDateString())
            ->get(['check_in', 'check_out']);

        foreach ($bookings as $booking) {
            foreach (CarbonPeriod::create($booking->check_in, $booking->check_out->copy()->subDay()) as $night) {
                $dates[$night->toDateString()] = true;
            }
        }

        $blocked = $this->product->blockedDates()
            ->where('end_date', '>=', $today->toDateString())
            ->get(['start_date', 'end_date']);

        foreach ($blocked as $range) {
            foreach (CarbonPeriod::create($range->start_date, $range->end_date) as $day) {
                $dates[$day->toDateString()] = true;
            }
        }

        return array_values(array_filter(
            array_keys($dates),
            fn (string $date) => $date <= $horizon->toDateString(),
        ));
    }

    public function render(): View
    {
        return view('livewire.booking.booking-widget', [
            'quote' => $this->quote(),
            'addons' => $this->product->addons()->where('is_active', true)->get(),
            'unavailableDates' => $this->unavailableDates(),
        ]);
    }
}
