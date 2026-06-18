<?php

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->service = app(BookingService::class);
});

it('confirms a pending booking and fires BookingConfirmed once', function () {
    Event::fake([BookingConfirmed::class]);
    $booking = Booking::factory()->pending()->create();

    $this->service->confirm($booking, 'pi_test_123');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->stripe_payment_intent_id)->toBe('pi_test_123')
        ->and($booking->expires_at)->toBeNull();

    Event::assertDispatchedTimes(BookingConfirmed::class, 1);
});

it('is idempotent on repeated confirmation (webhook redelivery)', function () {
    Event::fake([BookingConfirmed::class]);
    $booking = Booking::factory()->pending()->create();

    $this->service->confirm($booking, 'pi_test_123');
    $this->service->confirm($booking->fresh(), 'pi_test_123');

    Event::assertDispatchedTimes(BookingConfirmed::class, 1);
});

it('fires BookingConfirmed once when two webhook deliveries race', function () {
    Event::fake([BookingConfirmed::class]);
    $booking = Booking::factory()->pending()->create();

    // Simulate concurrent deliveries: both load the booking while it is still
    // pending (stale in-memory status), as separate webhook requests would.
    $first = Booking::find($booking->id);
    $second = Booking::find($booking->id);

    $this->service->confirm($first, 'pi_test_123');
    $this->service->confirm($second, 'pi_test_123');

    Event::assertDispatchedTimes(BookingConfirmed::class, 1);
});
