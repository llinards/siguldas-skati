<?php

namespace App\Services;

use App\Models\Product;
use App\Support\BookingQuote;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class PricingService
{
    /**
     * Build a price quote for a stay. The price is the sum of the per-night rates;
     * add-ons are request-only and never charged, so they do not affect the quote.
     */
    public function quote(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
    ): BookingQuote {
        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $nights = $checkIn->diffInDays($checkOut);

        $overrides = $product->prices()
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn ($price) => $price->date->toDateString());

        $nightlyRates = [];
        foreach (CarbonPeriod::create($checkIn, $checkOut->copy()->subDay()) as $night) {
            $key = $night->toDateString();
            $nightlyRates[] = $overrides->has($key) ? (int) $overrides[$key]->price : (int) $product->base_price;
        }

        $nightsTotal = array_sum($nightlyRates);

        return new BookingQuote(
            nights: $nights,
            nightsTotal: $nightsTotal,
            grandTotal: $nightsTotal,
            nightlyRates: $nightlyRates,
        );
    }
}
