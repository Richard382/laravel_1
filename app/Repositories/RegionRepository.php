<?php

namespace App\Repositories;

use App\Region;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegionRepository
{
    public static function all()
    {
        return Cache::rememberForever('regions', function ()
        {
            $items = [];
            $has_divider = false;

            $regions = Region::select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, ' . PHP_INT_MAX . ') `order`')])
                ->orderBy('order')
                ->get();

            foreach ($regions as $region)
            {
                if (! $has_divider && $region->order === PHP_INT_MAX)
                {
                    $has_divider = true;

                    $items[] = [
                        'divider' => true
                    ];
                }

                $items[] = $region;
            }

            return collect($items);
        });
    }
}
