<?php

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Product;
use App\Services\StripeService;
use Livewire\Livewire;

beforeEach(function () {
    $this->product = Product::factory()->create([
        'base_price' => 10000, 'cleaning_fee' => 3000, 'min_nights' => 1, 'person_count' => 4,
    ]);
});

it('renders and computes a live quote for chosen dates', function () {
    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->assertSet('quoteTotal', 33000) // 3x10000 + 3000
        ->assertSee('330'); // formatted euros somewhere in the summary
});

it('creates a pending booking and redirects to Stripe on reserve', function () {
    $fakeSession = \Stripe\Checkout\Session::constructFrom(['id' => 'cs_test_123', 'url' => 'https://checkout.stripe.test/cs_test_123']);

    $this->mock(StripeService::class, function ($mock) use ($fakeSession) {
        $mock->shouldReceive('createCheckoutSession')->once()->andReturn($fakeSession);
    });

    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-01')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('children', 0)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertRedirect('https://checkout.stripe.test/cs_test_123');

    expect(Booking::where('product_id', $this->product->id)->where('status', \App\Enums\BookingStatus::Pending)->count())->toBe(1);
});

it('shows an error and does not redirect when dates are unavailable', function () {
    Booking::factory()->for($this->product)->create([
        'status' => \App\Enums\BookingStatus::Confirmed, 'check_in' => '2026-09-01', 'check_out' => '2026-09-10',
    ]);

    Livewire::test(\App\Livewire\Booking\BookingWidget::class, ['product' => $this->product])
        ->set('checkIn', '2026-09-02')
        ->set('checkOut', '2026-09-04')
        ->set('adults', 2)
        ->set('guestName', 'Jane Guest')
        ->set('guestEmail', 'jane@example.com')
        ->set('guestPhone', '+37120000000')
        ->call('reserve')
        ->assertNoRedirect();

    expect(Booking::where('status', \App\Enums\BookingStatus::Pending)->count())->toBe(0);
});
