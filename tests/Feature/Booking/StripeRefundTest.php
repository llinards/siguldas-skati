<?php

use App\Models\Booking;
use App\Services\StripeService;

it('throws when Stripe is not configured', function () {
    $booking = Booking::factory()->create(['stripe_payment_intent_id' => 'pi_123']);

    expect(fn () => app(StripeService::class)->createRefund($booking))
        ->toThrow(RuntimeException::class);
});
