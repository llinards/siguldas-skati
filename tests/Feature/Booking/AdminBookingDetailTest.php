<?php

use App\Enums\BookingStatus;
use App\Livewire\Admin\Booking\BookingDetail;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\User;
use App\Services\StripeService;
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

it('saves admin notes', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create();

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('notes', 'Called the guest, all good')
        ->call('saveNotes');

    expect($booking->fresh()->notes)->toBe('Called the guest, all good');
});
