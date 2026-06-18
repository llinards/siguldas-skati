<?php

namespace App\Livewire\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
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

    public bool $wantsSaunaJacuzzi = false;

    public bool $wantsBabyCot = false;

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
        $this->adults = max(1, min($this->adults, $this->maxAdults()));
        $this->children = max(0, min($this->children, $this->maxChildren()));
    }

    public function incrementAdults(): void
    {
        if ($this->adults >= $this->maxAdults()) {
            $this->guestError = $this->product->children_count > 0
                ? __('Šī māja paredzēta līdz :count pieaugušajiem.', ['count' => $this->product->person_count])
                : __('Šī māja paredzēta līdz :count viesiem.', ['count' => $this->totalCapacity()]);

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
        if ($this->children >= $this->maxChildren()) {
            $this->guestError = $this->product->children_count > 0
                ? __('Šī māja paredzēta līdz :count bērniem.', ['count' => $this->product->children_count])
                : __('Šī māja paredzēta līdz :count viesiem.', ['count' => $this->totalCapacity()]);

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
     * Max adults: the base spots. When children share those spots (no
     * dedicated child spots), adults yield room to the children already added.
     */
    public function maxAdults(): int
    {
        return $this->product->children_count > 0
            ? $this->product->person_count
            : max(1, $this->product->person_count - $this->children);
    }

    /**
     * Max children: the dedicated child spots when the house sets them,
     * otherwise the base spots left over after the adults.
     */
    public function maxChildren(): int
    {
        return $this->product->children_count > 0
            ? $this->product->children_count
            : max(0, $this->product->person_count - $this->adults);
    }

    /**
     * Total guests the house can hold (base spots plus any dedicated child spots).
     */
    public function totalCapacity(): int
    {
        return $this->product->children_count > 0
            ? $this->product->person_count + $this->product->children_count
            : $this->product->person_count;
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
                ['sauna_jacuzzi' => $this->wantsSaunaJacuzzi, 'baby_cot' => $this->wantsBabyCot],
                ['name' => $this->guestName, 'email' => $this->guestEmail, 'phone' => $this->guestPhone],
            );
        } catch (BookingException $e) {
            $this->bookingError = $e->getMessage();

            return null;
        }

        $session = $stripe->createCheckoutSession(
            $booking,
            route('booking.manage', ['booking' => $booking->reference, 'token' => $booking->management_token]),
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

    /**
     * Per-date price overrides (in cents) within the booking horizon, keyed by
     * date. Dates without an override fall back to the product's base price.
     *
     * @return array<string, int>
     */
    private function priceOverrides(): array
    {
        $today = Carbon::today();
        $horizon = $today->copy()->addMonths(18);

        return $this->product->prices()
            ->whereBetween('date', [$today->toDateString(), $horizon->toDateString()])
            ->get(['date', 'price'])
            ->mapWithKeys(fn ($price) => [$price->date->toDateString() => (int) $price->price])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.booking.booking-widget', [
            'quote' => $this->quote(),
            'unavailableDates' => $this->unavailableDates(),
            'priceOverrides' => $this->priceOverrides(),
        ]);
    }
}
