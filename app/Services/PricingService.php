<?php

namespace App\Services;

use App\Enums\AddonPricingType;
use App\Models\Addon;
use App\Models\Product;
use App\Support\BookingQuote;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class PricingService
{
    /**
     * Build a price quote for a stay.
     *
     * @param  array<int, array{addon: Addon, quantity: int}>  $addonSelections
     */
    public function quote(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        array $addonSelections = [],
    ): BookingQuote {
        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $nights = $checkIn->diffInDays($checkOut);

        $overrides = $product->prices()
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn ($price) => $price->date->toDateString());

        $nightsTotal = 0;
        foreach (CarbonPeriod::create($checkIn, $checkOut->copy()->subDay()) as $night) {
            $key = $night->toDateString();
            $nightsTotal += $overrides->has($key) ? $overrides[$key]->price : $product->base_price;
        }

        $addonsTotal = 0;
        $addonLines = [];
        foreach ($addonSelections as $selection) {
            /** @var Addon $addon */
            $addon = $selection['addon'];
            $quantity = max(1, (int) ($selection['quantity'] ?? 1));
            $multiplier = $addon->pricing_type === AddonPricingType::PerNight ? $nights : 1;
            $lineTotal = $addon->price * $quantity * $multiplier;
            $addonsTotal += $lineTotal;

            $addonLines[] = [
                'addon_id' => $addon->id,
                'name' => $addon->getTranslation('name', app()->getLocale()),
                'price' => $addon->price,
                'pricing_type' => $addon->pricing_type->value,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        $cleaningFee = (int) $product->cleaning_fee;
        $grandTotal = $nightsTotal + $cleaningFee + $addonsTotal;

        return new BookingQuote(
            nights: $nights,
            nightsTotal: $nightsTotal,
            cleaningFee: $cleaningFee,
            addonsTotal: $addonsTotal,
            grandTotal: $grandTotal,
            addonLines: $addonLines,
        );
    }
}
