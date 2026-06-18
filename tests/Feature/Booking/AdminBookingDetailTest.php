<?php

use App\Enums\BookingStatus;
use App\Livewire\Admin\Booking\BookingDetail;
use App\Mail\BookingConfirmedCustomerMail;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Stripe\Refund;

it('shows booking details and requested add-ons to an admin', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['reference' => 'SS-DET1']);
    $addon = Addon::factory()->create();
    $booking->addons()->attach($addon->id, [
        'name' => 'Pirts', 'price' => 0, 'pricing_type' => 'per_stay', 'quantity' => 1,
    ]);

    $this->actingAs($user)->get('/lv/dashboard/booking/'.$booking->id)
        ->assertOk()
        ->assertSee('SS-DET1')
        ->assertSee('Pirts')
        ->assertSee('Pieaugušie')
        ->assertSee('Bērni');
});

it('lets an admin refund a confirmed booking any time', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => now()->addDay()->toDateString(), // inside the 7-day window — admin can still refund
        'grand_total' => 54000,
        'stripe_payment_intent_id' => 'pi_admin',
    ]);

    $this->mock(StripeService::class, function ($mock) {
        $mock->shouldReceive('createRefund')->once()
            ->andReturn(Refund::constructFrom(['id' => 're_admin', 'amount' => 54000]));
    });

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('refundReason', 'Owner cancelled')
        ->call('refund');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->fresh()->cancellation_reason)->toBe('Owner cancelled');
});

it('shows the cancellation reason for a cancelled booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Cancelled,
        'cancellation_reason' => 'Owner requested',
    ]);

    $this->actingAs($user)->get('/lv/dashboard/booking/'.$booking->id)
        ->assertOk()
        ->assertSee('Atcelšanas iemesls')
        ->assertSee('Owner requested');
});

it('lets an admin confirm a stuck pending booking and emails the customer', function () {
    Mail::fake();

    $user = User::factory()->create();
    $booking = Booking::factory()->pending()->create();

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->call('confirmBooking');

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->fresh()->expires_at)->toBeNull();

    Mail::assertQueued(BookingConfirmedCustomerMail::class);
});

it('does not confirm a booking that is not pending', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['status' => BookingStatus::Cancelled]);

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->call('confirmBooking');

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});

it('saves admin notes', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create();

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('notes', 'Called the guest, all good')
        ->call('saveNotes');

    expect($booking->fresh()->notes)->toBe('Called the guest, all good');
});
