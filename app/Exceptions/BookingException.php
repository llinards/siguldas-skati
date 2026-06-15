<?php

namespace App\Exceptions;

use RuntimeException;

class BookingException extends RuntimeException
{
    public static function datesUnavailable(): self
    {
        return new self(__('Izvēlētie datumi vairs nav pieejami.'));
    }

    public static function exceedsCapacity(int $max): self
    {
        return new self(__('Šī māja paredzēta līdz :max viesiem.', ['max' => $max]));
    }

    public static function belowMinimumNights(int $min): self
    {
        return new self(__('Minimālais uzturēšanās ilgums ir :min nakts(-is).', ['min' => $min]));
    }
}
