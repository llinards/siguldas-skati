<?php

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Event;
use Stripe\Refund;

it('refunds, cancels, records refund fields, and fires BookingCancelled', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_abc',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_1', 'amount' => 54000]));
    });

    app(BookingService::class)->cancelAndRefund($booking, amount: null, reason: 'Guest request');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_1')
        ->and($booking->cancellation_reason)->toBe('Guest request')
        ->and($booking->cancelled_at)->not->toBeNull()
        ->and($booking->refunded_at)->not->toBeNull();

    Event::assertDispatched(BookingCancelled::class, fn ($e) => $e->refunded === true);
});

it('does not re-dispatch when a racing webhook already cancelled the booking', function () {
    Event::fake([BookingCancelled::class]);

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_race',
    ]);

    // Stripe still returns the refund (idempotency key dedupes the actual charge).
    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_race', 'amount' => 54000]));
    });

    // Simulate the charge.refunded webhook landing mid-flight: the row is
    // already Cancelled in the DB while $booking is still a stale Confirmed model.
    Booking::whereKey($booking->getKey())->update(['status' => BookingStatus::Cancelled->value]);

    app(BookingService::class)->cancelAndRefund($booking, amount: null, reason: 'Guest request');

    Event::assertNotDispatched(BookingCancelled::class);
});

it('is idempotent — a second call does not refund again', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Cancelled,
        'stripe_payment_intent_id' => 'pi_abc',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    app(BookingService::class)->cancelAndRefund($booking);

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});
