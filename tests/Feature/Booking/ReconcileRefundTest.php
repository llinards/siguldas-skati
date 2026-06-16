<?php

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Event;

it('records refund state from a webhook without calling Stripe and fires BookingCancelled', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_xyz',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    app(BookingService::class)->reconcileRefund($booking, 54000, 're_dash_1');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_dash_1');

    Event::assertDispatched(BookingCancelled::class, fn ($e) => $e->refunded === true);
});

it('is idempotent on a redelivered charge.refunded', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Cancelled,
        'stripe_payment_intent_id' => 'pi_xyz',
    ]);

    app(BookingService::class)->reconcileRefund($booking, 54000, 're_dash_1');

    Event::assertNotDispatched(BookingCancelled::class);
});
