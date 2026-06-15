<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Console\Command;

class ReleaseExpiredBookings extends Command
{
    protected $signature = 'bookings:release-expired';

    protected $description = 'Release expired pending booking holds so the dates become available again.';

    public function handle(): int
    {
        $released = Booking::query()
            ->where('status', BookingStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => BookingStatus::Expired]);

        $this->info("Released {$released} expired booking hold(s).");

        return self::SUCCESS;
    }
}
