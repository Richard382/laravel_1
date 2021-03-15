<?php

namespace App\Repositories;

use App\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CityRepository
{
    public static function all()
    {
        return Cache::rememberForever('cities', function ()
        {
            $items = [];
            $has_divider = false;

            $cities = City::select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, ' . PHP_INT_MAX . ') `order`')])
                ->orderBy('order')
                ->get();

            foreach ($cities as $city)
            {
                if (! $has_divider && $city->order === PHP_INT_MAX)
                {
                    $has_divider = true;

                    $items[] = [
                        'divider' => true
                    ];
                }

                $items[] = $city;
            }

            return collect($items);
        });
    }
}
