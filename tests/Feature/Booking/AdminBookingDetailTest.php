<?php

use App\Enums\BookingStatus;
use App\Livewire\Admin\Booking\BookingDetail;
use App\Mail\BookingConfirmedCustomerMail;
use App\Models\Booking;
use App\Models\Product;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Stripe\Refund;

it('shows booking details and requested extras to an admin', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['title' => ['lv' => 'Skatu māja']]);
    $booking = Booking::factory()->for($product)->create([
        'reference' => 'SS-DET1',
        'wants_sauna_jacuzzi' => true,
        'wants_baby_cot' => true,
    ]);

    $this->actingAs($user)->get('/lv/dashboard/booking/'.$booking->id)
        ->assertOk()
        ->assertSee('SS-DET1')
        ->assertSee('Skatu māja')
        ->assertSee('Sauna un džakuzi')
        ->assertSee('Bērnu gultiņa')
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

it('lets an admin change the dates of a confirmed booking', function () {
    Mail::fake();

    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    $user = User::factory()->create();
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03',
    ]);

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('newCheckIn', '2026-09-10')
        ->set('newCheckOut', '2026-09-13')
        ->call('changeDates');

    expect($booking->fresh()->check_in->toDateString())->toBe('2026-09-10')
        ->and($booking->fresh()->grand_total)->toBe(30000);
});

it('previews the new price and difference as the dates change', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    $user = User::factory()->create();
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03', // 2 nights = 20000
        'grand_total' => 20000,
    ]);

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('newCheckIn', '2026-09-10')
        ->set('newCheckOut', '2026-09-13') // 3 nights = 30000 cents
        ->assertSee('300,00') // new price
        ->assertSee('100,00'); // +100,00 € difference to collect
});

it('rejects a check-out that is not after check-in', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03',
    ]);

    Livewire::actingAs($user)
        ->test(BookingDetail::class, ['booking' => $booking])
        ->set('newCheckIn', '2026-09-13')
        ->set('newCheckOut', '2026-09-10')
        ->call('changeDates')
        ->assertHasErrors('newCheckOut');

    expect($booking->fresh()->check_in->toDateString())->toBe('2026-08-01');
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
