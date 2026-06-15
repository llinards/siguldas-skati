<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\StripeService;
use Stripe\Event;

it('confirms the booking on checkout.session.completed', function () {
    $booking = Booking::factory()->pending()->create();

    $event = Event::constructFrom([
        'id' => 'evt_1',
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_test_1',
            'object' => 'checkout.session',
            'payment_intent' => 'pi_test_1',
            'metadata' => ['booking_id' => (string) $booking->id],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $response = $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake']);

    $response->assertOk();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->fresh()->stripe_payment_intent_id)->toBe('pi_test_1');
});

it('returns 400 on an invalid signature', function () {
    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('constructWebhookEvent')->andThrow(new \Stripe\Exception\SignatureVerificationException('bad sig'));
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 'bad'])->assertStatus(400);
});

it('ignores unrelated events with a 200', function () {
    $event = Event::constructFrom([
        'id' => 'evt_2', 'type' => 'payment_intent.created',
        'data' => ['object' => ['id' => 'pi_x']],
    ]);
    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 'ok'])->assertOk();
});
