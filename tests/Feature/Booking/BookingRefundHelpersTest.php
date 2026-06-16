<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;

it('formats the grand total in euros', function () {
    $booking = Booking::factory()->make(['grand_total' => 54000]);

    expect($booking->formattedTotal())->toBe('540,00 €');
});

it('formats the refund amount in euros, or a dash when none', function () {
    expect(Booking::factory()->make(['refund_amount' => 12050])->formattedRefund())->toBe('120,50 €')
        ->and(Booking::factory()->make(['refund_amount' => null])->formattedRefund())->toBe('—');
});

it('is guest-refundable only when confirmed and at least 7 days before check-in', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    // exactly 7 days out -> allowed
    expect(Booking::factory()->make([
        'status' => BookingStatus::Confirmed, 'check_in' => '2026-07-08',
    ])->isRefundableByGuest())->toBeTrue();

    // 6 days out -> blocked
    expect(Booking::factory()->make([
        'status' => BookingStatus::Confirmed, 'check_in' => '2026-07-07',
    ])->isRefundableByGuest())->toBeFalse();

    // not confirmed -> blocked
    expect(Booking::factory()->make([
        'status' => BookingStatus::Pending, 'check_in' => '2026-08-01',
    ])->isRefundableByGuest())->toBeFalse();

    Carbon::setTestNow();
});
