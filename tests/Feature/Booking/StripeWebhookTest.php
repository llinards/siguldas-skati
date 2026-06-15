<?php

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Services\StripeService;
use Illuminate\Support\Facades\Event as EventFacade;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;

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
        $mock->shouldReceive('constructWebhookEvent')->andThrow(new SignatureVerificationException('bad sig'));
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

it('confirms via stripe_session_id when metadata booking_id is absent', function () {
    $booking = Booking::factory()->pending()->create(['stripe_session_id' => 'cs_match_1']);

    $event = Event::constructFrom([
        'id' => 'evt_3',
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_match_1',
            'object' => 'checkout.session',
            'payment_intent' => 'pi_test_2',
            'metadata' => [],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $response = $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake']);

    $response->assertOk();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});

it('is idempotent when the same checkout.session.completed is received twice', function () {
    EventFacade::fake([BookingConfirmed::class]);

    $booking = Booking::factory()->pending()->create();

    $event = Event::constructFrom([
        'id' => 'evt_4',
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_test_idem',
            'object' => 'checkout.session',
            'payment_intent' => 'pi_test_idem',
            'metadata' => ['booking_id' => (string) $booking->id],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->twice()->andReturn($event);
    });

    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();
    $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake'])->assertOk();

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
    EventFacade::assertDispatchedTimes(BookingConfirmed::class, 1);
});

it('honors a late successful payment for an expired-hold booking', function () {
    $booking = Booking::factory()->expired()->create();

    $event = Event::constructFrom([
        'id' => 'evt_5',
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_test_expired',
            'object' => 'checkout.session',
            'payment_intent' => 'pi_test_expired',
            'metadata' => ['booking_id' => (string) $booking->id],
        ]],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event) {
        $mock->shouldReceive('constructWebhookEvent')->once()->andReturn($event);
    });

    $response = $this->postJson('/stripe/webhook', [], ['Stripe-Signature' => 't=1,v1=fake']);

    $response->assertOk();
    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);
});
