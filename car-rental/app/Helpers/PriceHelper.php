<?php

namespace App\Helpers;

class PriceHelper
{
    public static function applyDiscount($price, $percent)
    {
        return $price - ($price * $percent / 100);
    }
}
