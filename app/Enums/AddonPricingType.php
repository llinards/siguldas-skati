<?php

namespace App\Enums;

enum AddonPricingType: string
{
    case PerStay = 'per_stay';
    case PerNight = 'per_night';
}
