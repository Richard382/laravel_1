<?php

namespace App\Helpers;

class Shop
{
    public static function formatPrice($price)
    {
        return number_format($price, 2) . env('SHOP_CURRENCY');
    }
}
