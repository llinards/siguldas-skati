<?php

use App\Enums\BookingStatus;
use App\Models\Booking;

it('shows the confirmed reference on the success page', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed, 'reference' => 'SS-ABCDE',
    ]);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee('SS-ABCDE');
});

it('shows a processing state when the booking is still pending', function () {
    $booking = Booking::factory()->pending()->create(['reference' => 'SS-PEND1']);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee('Apstiprinām jūsu maksājumu');
});

it('renders the cancel page', function () {
    $booking = Booking::factory()->pending()->create();

    $this->get('/lv/booking/'.$booking->reference.'/cancel')->assertOk();
});
