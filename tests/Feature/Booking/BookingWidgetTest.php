<?php

use App\Enums\BookingStatus;
use App\Livewire\Booking\BookingWidget;
use App\Models\Booking;
use App\Models\Product;
use App\Services\StripeService;
use Livewire\Livewire;
use Stripe\Checkout\Session;

beforeEach(function () {
    $this->product = Product::factory()->create([
        'base_price' => 10000, 'min_nights' => 1, 'person_count' => 4,
    ]);
});

it('renders and computes a live quote for chosen dates', function () {
    Livewire::test(BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->assertSet('quoteTotal', 30000) // 3 nights x 10000, nights only
        ->assertSee('300'); // formatted euros somewhere in the summary
});

it('syncs the calendar date range via selectDates', function () {
    Livewire::test(BookingWidget::class, ['product' => $this->product])
        ->call('selectDates', '2026-09-01', '2026-09-04')
        ->assertSet('checkIn', '2026-09-01')
        ->assertSet('checkOut', '2026-09-04')
        ->assertSet('quoteTotal', 30000);
});

it('exposes occupied nights as unavailable dates for the calendar', function () {
    $checkIn = now()->addMonth()->startOfMonth();          // within the 18-month horizon
    $checkOut = $checkIn->copy()->addDays(3);

    Booking::factory()->for($this->product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => $checkIn->toDateString(),
        'check_out' => $checkOut->toDateString(),
    ]);

    Livewire::test(BookingWidget::class, ['product' => $this->product])
        ->assertViewHas('unavailableDates', function (array $dates) use ($checkIn, $checkOut) {
            // Occupied nights are check_in..check_out-1; the checkout day stays bookable (back-to-back).
            return in_array($checkIn->toDateString(), $dates, true)
                && in_array($checkIn->copy()->addDays(2)->toDateString(), $dates, true)
                && ! in_array($checkOut->toDateString(), $dates, true);
        });
});

it('caps adults at the house limit and reports an error when exceeded', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'person_count' => 2, 'children_count' => 0]);

    Livewire::test(BookingWidget::class, ['product' => $product])
        ->assertSet('adults', 2) // default clamped to capacity
        ->call('incrementAdults')
        ->assertSet('adults', 2) // not incremented past the cap
        ->assertNotSet('guestError', null)
        ->call('decrementAdults')
        ->assertSet('adults', 1)
        ->call('decrementAdults')
        ->assertSet('adults', 1); // never below 1
});

it('caps children at the house limit', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'person_count' => 4, 'children_count' => 1]);

    Livewire::test(BookingWidget::class, ['product' => $product])
        ->assertSet('children', 0)
        ->call('incrementChildren')
        ->assertSet('children', 1)
        ->call('incrementChildren')
        ->assertSet('children', 1) // capped
        ->assertNotSet('guestError', null);
});

it('caps the combined guests at the house total', function () {
    // total 3, generous individual caps
    $product = Product::factory()->create(['base_price' => 10000, 'person_count' => 4, 'children_count' => 4, 'max_guests' => 3]);

    Livewire::test(BookingWidget::class, ['product' => $product])
        ->assertSet('adults', 2) // clamped to total/cap
        ->call('incrementChildren')
        ->assertSet('children', 1) // total now 3 = max
        ->call('incrementChildren')
        ->assertSet('children', 1) // blocked by total
        ->assertNotSet('guestError', null)
        ->call('incrementAdults')
        ->assertSet('adults', 2); // also blocked by total
});

it('creates a pending booking and redirects to Stripe on reserve', function () {
    $fakeSession = Session::constructFrom(['id' => 'cs_test_123', 'url' => 'https://checkout.stripe.test/cs_test_123']);

    $this->mock(StripeService::class, function ($mock) use ($fakeSession) {
        $mock->shouldReceive('createCheckoutSession')->once()->andReturn($fakeSession);
    });

    Livewire::test(BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('children', 0)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertRedirect('https://checkout.stripe.test/cs_test_123');

    expect(Booking::where('product_id', $this->product->id)->where('status', BookingStatus::Pending)->count())->toBe(1);
});

it('shows an error and does not redirect when dates are unavailable', function () {
    Booking::factory()->for($this->product)->create([
        'status' => BookingStatus::Confirmed, 'check_in' => '2026-09-01', 'check_out' => '2026-09-10',
    ]);

    Livewire::test(BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-02')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertNoRedirect();

    expect(Booking::where('status', BookingStatus::Pending)->count())->toBe(0);
});
