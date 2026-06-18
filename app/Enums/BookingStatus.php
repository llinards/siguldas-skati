<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    /**
     * Latvian source label used as the translation key for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Gaida apmaksu',
            self::Confirmed => 'Apstiprināta',
            self::Expired => 'Beidzies termiņš',
            self::Cancelled => 'Atcelta',
        };
    }
}
