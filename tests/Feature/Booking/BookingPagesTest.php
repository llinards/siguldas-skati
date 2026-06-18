<?php

use App\Models\Booking;

it('renders the cancel page with a contact-us line', function () {
    $booking = Booking::factory()->pending()->create();

    $this->get('/lv/booking/'.$booking->reference.'/cancel')
        ->assertOk()
        ->assertSee('+371 25666622')
        ->assertSee('Maksājums atcelts | '.config('app.name'));
});

it('shows the contact-us line on a 404 page', function () {
    $this->get('/lv/this-route-does-not-exist-xyz')
        ->assertNotFound()
        ->assertSee('+371 25666622');
});
