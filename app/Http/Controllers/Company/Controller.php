<?php

namespace App\Http\Controllers\Company;

use App\BrokerVisibility;
use App\Company;
use App\Inquiry;
use App\PropertyType;
use App\Repositories\CityRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\RegionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Controller extends \App\Http\Controllers\Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $inquiry = false;

        if ($request->has('inquiry') && $request->get('inquiry') && $request->get('inquiry') !== 'false') {

            $inquiry = Inquiry::owns()->where('id', $request->get('inquiry'))->firstOrFail();
        }

        if ($request->has('inquiry_token') && $request->get('inquiry_token') && $request->get('inquiry_token') !== 'false') {
            $inquiry = Inquiry::FindByToken($request->get('inquiry_token'))->firstOrFail();
        }

        if ($request->ajax())
        {
            $companies = CompanyRepository::list(
                $request->get('search'),
                $request->get('region'),
                $request->get('type'),
                $request->get('properties')
            );

            if ($inquiry) {
                $companies->getCollection()->transform(function ($company) use ($inquiry) {
                    $company->canContact = true;

                    if ($inquiry->hasOfferFrom($company)) {
                        $company->canContact = false;
                    }

                    return $company;
                });
            }

            return response()->json([
                'pages_number' => $companies->lastPage(),
                'total_visible' => $companies->perPage(),
                'current_page' => $companies->currentPage(),
                'items' => $companies->items()
            ], 200);
        }

        $companies = CompanyRepository::top();

        if ($inquiry) {
            $companies = $companies->map(function ($company) use ($inquiry) {
                $company->canContact = true;

                if ($inquiry->hasOfferFrom($company)) {
                    $company->canContact = false;
                }

                return $company;
            });
        }

        $regions = RegionRepository::all();

        $property_types = Cache::rememberForever('property_types', function () {
            return PropertyType::with('properties')->get();
        });

        $top_number = Cache::rememberForever('top_number', function () {
           return BrokerVisibility::all()->count();
        });

        return view('companies.index', compact('inquiry','top_number', 'companies', 'regions', 'property_types'));
    }

    /**
     * @param Company $company
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Company $company)
    {
        $comments = $company->getComments();
        $spheres = $company->getSpheres();
        $languages = $company->getLanguages();

        return view('companies.view', compact('company', 'comments', 'spheres', 'languages'));
    }
}
