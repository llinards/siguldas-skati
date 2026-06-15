<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Product;
use Carbon\CarbonInterface;

class AvailabilityService
{
    /**
     * Whether the half-open range [checkIn, checkOut) is bookable.
     */
    public function isAvailable(
        Product $product,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        ?int $ignoreBookingId = null,
    ): bool {
        $checkInDate = $checkIn->toDateString();
        $checkOutDate = $checkOut->toDateString();

        // Overlap (half-open ranges): existing.check_in < new.checkOut AND existing.check_out > new.checkIn.
        // whereDate normalizes the date columns so this is correct on both SQLite (test) and MySQL (prod).
        $bookingClash = $product->bookings()
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where(function ($query) {
                $query->where('status', BookingStatus::Confirmed)
                    ->orWhere(function ($pending) {
                        $pending->where('status', BookingStatus::Pending)
                            ->where('expires_at', '>', now());
                    });
            })
            ->whereDate('check_in', '<', $checkOutDate)
            ->whereDate('check_out', '>', $checkInDate)
            ->exists();

        if ($bookingClash) {
            return false;
        }

        // blocked_dates are inclusive (start_date..end_date are blocked nights).
        // Overlap with stay nights [checkIn, checkOut): start_date < checkOut AND end_date >= checkIn.
        // Pure column comparisons — driver-agnostic (SQLite test + MySQL prod).
        $blockedClash = $product->blockedDates()
            ->where('start_date', '<', $checkOutDate)
            ->where('end_date', '>=', $checkInDate)
            ->exists();

        return ! $blockedClash;
    }
}
