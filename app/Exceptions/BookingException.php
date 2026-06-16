<?php

namespace App\Exceptions;

use RuntimeException;

class BookingException extends RuntimeException
{
    public static function datesUnavailable(): self
    {
        return new self(__('Izvēlētie datumi vairs nav pieejami.'));
    }

    public static function tooManyChildren(int $max): self
    {
        return new self(__('Šī māja paredzēta līdz :count bērniem.', ['count' => $max]));
    }

    public static function tooManyGuests(int $max): self
    {
        return new self(__('Šī māja paredzēta līdz :count viesiem kopā.', ['count' => $max]));
    }

    public static function belowMinimumNights(int $min): self
    {
        return new self(__('Minimālais uzturēšanās ilgums ir :min nakts(-is).', ['min' => $min]));
    }
}
