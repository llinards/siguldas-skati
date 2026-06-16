<?php

namespace App\Support;

readonly class BookingQuote
{
    public function __construct(
        public int $nights,
        public int $nightsTotal,
        public int $grandTotal,
    ) {}
}
