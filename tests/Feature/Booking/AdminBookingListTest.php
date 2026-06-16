<?php

use App\Models\Booking;
use App\Models\User;

it('requires authentication', function () {
    $this->get('/lv/dashboard/bookings')->assertRedirect();
});

it('lists bookings for an authenticated admin', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['reference' => 'SS-ADM1', 'guest_name' => 'Anna Guest']);

    $this->actingAs($user)->get('/lv/dashboard/bookings')
        ->assertOk()
        ->assertSee('SS-ADM1')
        ->assertSee('Anna Guest');
});
