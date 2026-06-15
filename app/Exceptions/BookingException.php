<?php

namespace App\Exceptions;

use RuntimeException;

class BookingException extends RuntimeException
{
    public static function datesUnavailable(): self
    {
        return new self('The selected dates are no longer available.');
    }

    public static function exceedsCapacity(int $max): self
    {
        return new self("This house accommodates up to {$max} guests.");
    }

    public static function belowMinimumNights(int $min): self
    {
        return new self("The minimum stay is {$min} night(s).");
    }
}
