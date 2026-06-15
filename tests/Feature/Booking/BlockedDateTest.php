<?php

use App\Models\BlockedDate;
use App\Models\Product;

it('belongs to a product and stores an inclusive date range', function () {
    $product = Product::factory()->create();

    $blocked = BlockedDate::factory()->for($product)->create([
        'start_date' => '2026-07-10',
        'end_date' => '2026-07-12',
        'reason' => 'Maintenance',
    ]);

    expect($blocked->product->is($product))->toBeTrue()
        ->and($blocked->start_date->toDateString())->toBe('2026-07-10')
        ->and($product->fresh()->blockedDates)->toHaveCount(1);
});
