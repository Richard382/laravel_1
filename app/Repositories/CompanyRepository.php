<?php

namespace App\Repositories;

use App\Company;
use Illuminate\Support\Facades\DB;

class CompanyRepository
{
    /**
     * Get top rating companies
     *
     * @param int $number Number of items to return
     * @return mixed
     */
    public static function top($number = 5)
    {
        $companies =
            Company::select(
                'companies.*',
                //DB::raw('AVG(ratings.rating) as rating'),
                DB::raw('broker_visibilities.position as position')
            )
                ->whereHas('visibility')
                ->leftJoin('broker_visibilities', function($join) {
                    $join
                        ->on('broker_visibilities.company_id', '=', 'companies.id');
                })
//                ->leftJoin('ratings', function($join) {
//                    $join
//                        ->on('ratings.company_id', '=', 'companies.id');
//                })
                ->groupBy('companies.id')
                ->orderBy('position')
//                ->orderByDesc('rating')
                ->limit($number)
                ->get();

        return $companies;
    }

    public static function list($phrase = false, $region = false, $type = false, $properties = false)
    {
        $companies =
            Company::whereNotNull('id');

        if ($phrase)
        {
            $companies->where('name', 'like', '%'.$phrase.'%');
        }


        $companies->whereHas('spheres', function ($q) use ($region, $type, $properties) {
            if ($region)
            {
                $q->whereHas('regions', function ($q) use ($region) {
                    $q->where('regions.id', $region);
                });
            }

            if ($type)
            {
                $q->whereHas('type', function($q) use ($type) {
                    $q->where('property_types.id', $type);
                });
            }

            if ($properties)
            {
                $q->whereHas('property', function ($q) use ($properties) {
                    $q->whereIn('properties.id', $properties);
                });
            }
        });

        return $companies->paginate();
    }
}
