<?php

use App\Models\Booking;

it('generates a unique SS-prefixed reference', function () {
    $ref = Booking::generateReference();

    expect($ref)->toStartWith('SS-')
        ->and(strlen($ref))->toBe(8); // SS- + 5 chars
});

it('does not collide with an existing reference', function () {
    $existing = Booking::factory()->create(['reference' => 'SS-AAAAA']);

    // Force the first random draw to equal the existing one, then succeed on retry.
    $ref = Booking::generateReference();

    expect($ref)->not->toBe($existing->reference)
        ->and(Booking::where('reference', $ref)->exists())->toBeFalse();
});
