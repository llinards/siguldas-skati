<?php

namespace App\Support;

readonly class BookingQuote
{
    /**
     * @param  array<int, int>  $nightlyRates  Per-night price in cents, in stay order.
     */
    public function __construct(
        public int $nights,
        public int $nightsTotal,
        public int $grandTotal,
        public array $nightlyRates = [],
    ) {}

    /**
     * Whether every night in the stay costs the same (no per-date overrides in range).
     */
    public function hasUniformRate(): bool
    {
        return count(array_unique($this->nightlyRates)) === 1;
    }

    /**
     * The single per-night rate in cents when the stay is uniformly priced, else null.
     */
    public function uniformRate(): ?int
    {
        return $this->hasUniformRate() ? $this->nightlyRates[0] : null;
    }
}
