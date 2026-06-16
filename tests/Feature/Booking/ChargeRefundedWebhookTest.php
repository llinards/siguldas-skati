<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\StripeService;
use Stripe\Event;

it('reconciles a dashboard refund on charge.refunded', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_ref_1',
    ]);

    $event = Event::constructFrom([
        'id' => 'evt_r1',
        'type' => 'charge.refunded',
        'data' => ['object' => [
            'id' => 'ch_1',
            'object' => 'charge',
            'payment_intent' => 'pi_ref_1',
            'amount_refunded' => 54000,
            'refunds' => ['data' => [['id' => 're_hook_1']]],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->refund_amount)->toBe(54000)
        ->and($booking->stripe_refund_id)->toBe('re_hook_1');
});

it('ignores charge.refunded with no matching payment intent', function () {
    $event = Event::constructFrom([
        'id' => 'evt_r2',
        'type' => 'charge.refunded',
        'data' => ['object' => [
            'id' => 'ch_2', 'object' => 'charge',
            'payment_intent' => 'pi_unknown', 'amount_refunded' => 1000,
            'refunds' => ['data' => []],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();
});
