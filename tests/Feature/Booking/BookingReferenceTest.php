<?php

use App\Models\Booking;
use Illuminate\Support\Str;

it('generates a unique SS-prefixed reference', function () {
    $ref = Booking::generateReference();

    expect($ref)->toStartWith('SS-')
        ->and(strlen($ref))->toBe(8); // SS- + 5 chars
});

it('does not collide with an existing reference', function () {
    Booking::factory()->create(['reference' => 'SS-AAAAA']);

    $sequence = ['AAAAA', 'BBBBB'];
    Str::createRandomStringsUsing(function () use (&$sequence) {
        return array_shift($sequence);
    });

    try {
        $ref = Booking::generateReference();
        expect($ref)->toBe('SS-BBBBB');
    } finally {
        Str::createRandomStringsNormally();
    }
});
