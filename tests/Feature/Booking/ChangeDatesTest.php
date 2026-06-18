<?php

use App\Enums\BookingStatus;
use App\Events\BookingDatesChanged;
use App\Exceptions\BookingException;
use App\Mail\BookingUpdatedCustomerMail;
use App\Models\Booking;
use App\Models\Product;
use App\Services\BookingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

it('moves a booking to new dates and recomputes the total', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03', // 2 nights
        'nights_total' => 20000,
        'grand_total' => 20000,
    ]);

    app(BookingService::class)->changeDates($booking, Carbon::parse('2026-09-10'), Carbon::parse('2026-09-13')); // 3 nights

    $booking->refresh();
    expect($booking->check_in->toDateString())->toBe('2026-09-10')
        ->and($booking->check_out->toDateString())->toBe('2026-09-13')
        ->and($booking->nights_total)->toBe(30000)
        ->and($booking->grand_total)->toBe(30000);
});

it('rejects dates that clash with another booking', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-09-10',
        'check_out' => '2026-09-13',
    ]);
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03',
    ]);

    expect(fn () => app(BookingService::class)->changeDates($booking, Carbon::parse('2026-09-11'), Carbon::parse('2026-09-12')))
        ->toThrow(BookingException::class);

    expect($booking->fresh()->check_in->toDateString())->toBe('2026-08-01');
});

it('rejects a range shorter than the minimum nights', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 3]);
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-05',
    ]);

    expect(fn () => app(BookingService::class)->changeDates($booking, Carbon::parse('2026-09-10'), Carbon::parse('2026-09-11')))
        ->toThrow(BookingException::class);
});

it('can resize within its own date range (ignores its own dates)', function () {
    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-05',
    ]);

    app(BookingService::class)->changeDates($booking, Carbon::parse('2026-08-02'), Carbon::parse('2026-08-04'));

    expect($booking->fresh()->check_in->toDateString())->toBe('2026-08-02')
        ->and($booking->fresh()->check_out->toDateString())->toBe('2026-08-04');
});

it('dispatches BookingDatesChanged after a successful move', function () {
    Event::fake([BookingDatesChanged::class]);

    $product = Product::factory()->create(['base_price' => 10000, 'min_nights' => 1]);
    $booking = Booking::factory()->for($product)->create([
        'status' => BookingStatus::Confirmed,
        'check_in' => '2026-08-01',
        'check_out' => '2026-08-03',
    ]);

    app(BookingService::class)->changeDates($booking, Carbon::parse('2026-09-10'), Carbon::parse('2026-09-12'));

    Event::assertDispatched(BookingDatesChanged::class);
});

it('queues the customer update email when the dates change', function () {
    Mail::fake();

    $booking = Booking::factory()->create(['guest_email' => 'guest@example.com']);

    event(new BookingDatesChanged($booking));

    Mail::assertQueued(BookingUpdatedCustomerMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
});
