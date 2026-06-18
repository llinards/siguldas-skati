<?php

use App\Livewire\Admin\Booking\BookingList;
use App\Models\Booking;
use App\Models\User;
use Livewire\Livewire;

it('requires authentication', function () {
    $this->get('/lv/dashboard/bookings')->assertRedirect();
});

it('paginates at ten bookings per page', function () {
    $user = User::factory()->create();
    Booking::factory()->count(11)->create();

    Livewire::actingAs($user)->test(BookingList::class)
        ->assertViewHas('bookings', fn ($bookings) => $bookings->perPage() === 10 && $bookings->count() === 10);
});

it('orders bookings by check-in date ascending', function () {
    $user = User::factory()->create();
    Booking::factory()->create(['reference' => 'SS-LATER', 'check_in' => '2026-09-20', 'check_out' => '2026-09-23']);
    Booking::factory()->create(['reference' => 'SS-SOON', 'check_in' => '2026-07-01', 'check_out' => '2026-07-04']);

    Livewire::actingAs($user)->test(BookingList::class)
        ->assertViewHas('bookings', fn ($bookings) => $bookings->first()->reference === 'SS-SOON');
});

it('permanently deletes a booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create();

    Livewire::actingAs($user)->test(BookingList::class)
        ->call('delete', $booking->id);

    expect(Booking::find($booking->id))->toBeNull();
});

it('lists bookings for an authenticated admin', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['reference' => 'SS-ADM1', 'guest_name' => 'Anna Guest']);

    $this->actingAs($user)->get('/lv/dashboard/bookings')
        ->assertOk()
        ->assertSee('SS-ADM1')
        ->assertSee('Anna Guest');
});
