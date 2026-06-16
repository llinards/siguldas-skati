<?php

use App\Enums\BookingStatus;
use App\Livewire\Booking\ManageBooking;
use App\Models\Booking;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Stripe\Refund;

it('aborts with 403 on a bad management token', function () {
    $booking = Booking::factory()->create();

    $this->get('/lv/booking/'.$booking->reference.'/manage/'.Str::uuid())
        ->assertForbidden();
});

it('renders the manage page with a valid token', function () {
    $booking = Booking::factory()->create(['reference' => 'SS-MNG1']);

    $this->get('/lv/booking/'.$booking->reference.'/manage/'.$booking->management_token)
        ->assertOk()
        ->assertSee('SS-MNG1');
});

it('lets the guest refund when at least 7 days before check-in', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-20',
        'check_out' => '2026-07-23',
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_mng',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_mng', 'amount' => 54000]));
    });

    Livewire::test(ManageBooking::class, ['booking' => $booking, 'token' => $booking->management_token])
        ->call('requestRefund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);

    Carbon::setTestNow();
});

it('blocks a refund inside the 7-day window', function () {
    Carbon::setTestNow('2026-07-01 10:00:00');

    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-07-05',
        'check_out' => '2026-07-08',
        'stripe_payment_intent_id' => 'pi_mng2',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->never();
    });

    Livewire::test(ManageBooking::class, ['booking' => $booking, 'token' => $booking->management_token])
        ->call('requestRefund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed);

    Carbon::setTestNow();
});
