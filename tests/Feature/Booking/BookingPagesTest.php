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

it('links to the manage page when the booking is confirmed', function () {
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Confirmed,
    ]);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee(route('booking.manage', [
            'booking' => $booking->reference,
            'token' => $booking->management_token,
        ]));
});

it('does not link to the manage page when the booking is still pending', function () {
    $booking = Booking::factory()->pending()->create();

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertDontSee(route('booking.manage', [
            'booking' => $booking->reference,
            'token' => $booking->management_token,
        ]));
});

it('shows a processing state when the booking is still pending', function () {
    $booking = Booking::factory()->pending()->create(['reference' => 'SS-PEND1']);

    $this->get('/lv/booking/'.$booking->reference.'/success')
        ->assertOk()
        ->assertSee('Apstiprinām jūsu maksājumu');
});

it('renders the cancel page with a contact-us line', function () {
    $booking = Booking::factory()->pending()->create();

    $this->get('/lv/booking/'.$booking->reference.'/cancel')
        ->assertOk()
        ->assertSee('+371 25666622');
});

it('shows the contact-us line on a 404 page', function () {
    $this->get('/lv/this-route-does-not-exist-xyz')
        ->assertNotFound()
        ->assertSee('+371 25666622');
});
