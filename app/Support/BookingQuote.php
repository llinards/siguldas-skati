<?php

namespace App\Support;

/**
 * @phpstan-type AddonLine array{addon_id: int, name: string, price: int, pricing_type: string, quantity: int, line_total: int}
 */
readonly class BookingQuote
{
    /**
     * @param  array<int, array{addon_id: int, name: string, price: int, pricing_type: string, quantity: int, line_total: int}>  $addonLines
     */
    public function __construct(
        public int $nights,
        public int $nightsTotal,
        public int $cleaningFee,
        public int $addonsTotal,
        public int $grandTotal,
        public array $addonLines = [],
    ) {}
}
