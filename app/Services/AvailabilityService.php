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

        // Overlap: existing.check_in < new.checkOut AND existing.check_out > new.checkIn.
        // We use 'YYYY-MM-DD 99:99:99' surrogate to make the comparison work uniformly on
        // SQLite (stores dates as 'YYYY-MM-DD 00:00:00') and MySQL (stores DATE as 'YYYY-MM-DD').
        // Comparing existing.check_out against checkInDate + ' 99:99:99' ensures that a booking
        // with check_out = checkInDate (either format) is NOT treated as a clash — allowing
        // back-to-back stays. Pure string comparisons — no date functions.
        $bookingClash = $product->bookings()
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where(function ($query) {
                $query->where('status', BookingStatus::Confirmed)
                    ->orWhere(function ($pending) {
                        $pending->where('status', BookingStatus::Pending)
                            ->where('expires_at', '>', now());
                    });
            })
            ->where('check_in', '<', $checkOutDate)
            ->where('check_out', '>', $checkInDate.' 99:99:99')
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
