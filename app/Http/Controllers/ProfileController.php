<?php

namespace App\Http\Controllers;

use App\Language;
use App\PropertyType;
use App\Repositories\BrokerRepository;
use App\Repositories\RegionRepository;
use App\Repositories\UserRepository;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (Auth::user()->isBroker()) {
            // Base validation for Authenticated
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'agency' => ['required', 'string', 'max:255'],
                'paths' => ['required', 'array'],
                'language' => ['required', 'array', 'exists:languages,id'],
                'description' => ['required', 'string'],
                'phone' => ['required', 'regex:/^(\+370)\d{8}$/i'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,id,' . Auth::user()->id],
                'website' => ['max:255'],
                'represent_video'   => ['nullable', 'max:255'],
                'avatar' => ['imageable'],
                'company_logo' => ['imageable'],
                'certificates.*' => ['image'],
                'password' => ['string', 'min:8', 'confirmed'],
            ];

            // Validate paths
            if (isset($data['paths'])) {
                foreach ($data['paths'] as $key => $path) {
                    $rules['paths.' . $key . '.property_type'] = ['required', 'exists:property_types,id'];
                    $rules['paths.' . $key . '.properties'] = ['required', 'exists:properties,id'];
                    $rules['paths.' . $key . '.regions'] = ['required', 'exists:cities,id'];
                    $rules['paths.' . $key . '.services'] = ['required', 'exists:services,id'];
                    $rules['paths.' . $key . '.price_from'] = ['required', 'numeric', 'lt:' . 'paths.' . $key . '.price_to'];
                    $rules['paths.' . $key . '.price_to'] = ['required', 'numeric'];
                }
            }
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,id,' . Auth::user()->id],
                'phone' => ['required', 'regex:/^(\+370)\d{8}$/i'],
                'password' => ['string', 'min:8', 'confirmed'],
            ];
        }

        return Validator::make($data, $rules);
    }

    public function me(Request $request)
    {
//        print_r(\Auth::user()->toArray());exit;
        $becamebroker = $request->route('becamebroker');
        $languages = Cache::rememberForever('languages', function () {
            return Language::all();
        });

//        $property_types = Cache::rememberForever('property_types', function () {
//            return PropertyType::with('properties')->get();
//        });
//
//        $services = Cache::rememberForever('services-broker', function () {
//            return Service::typeBroker();
//        });
        
        $property_types = \App\ChainPropertyType::select('property_types.id','chain_property_type.id as chain_property_type_id',
                    \DB::raw('if(property_types.provider_display_name = "", property_types.name, property_types.provider_display_name) as name')
                )
                ->join('property_types', 'property_types.id', 'chain_property_type.property_type_id')
                ->get();
            foreach($property_types as $key2=>$propertyType){
                $properties = \App\ChainProperty::select('properties.id','chain_properties.id as chain_properties_id',
                        \DB::raw('if(properties.provider_display_name = "", properties.name, properties.provider_display_name) as name')
                        )
                    ->join('properties', 'properties.id', 'chain_properties.property_id')
                    ->where('chain_properties.chain_property_type_id', $propertyType['chain_property_type_id'])
                    ->get();
                foreach($properties as $key3=>$property){
                    $services = \App\ChainService::select('services.id',
                            \DB::raw('if(services.provider_display_name = "", services.type_broker, services.provider_display_name) as name')
                            )
                        ->join('services', 'chain_service.service_id', 'services.id')
                        ->where('chain_service.chain_property_id', $property['chain_properties_id'])
                        ->get();
                    $properties[$key3]['services'] = $services;
                }
                $property_types[$key2]['properties'] = $properties;
            }
//        print_r(json_encode($property_types));exit;
        $services = \App\ChainService::select('services.id',
                \DB::raw('if(services.provider_display_name = "", services.type_broker, services.provider_display_name) as name')
                )
                ->join('services','services.id','chain_service.service_id')
                ->get();

        $cityIds = 
//                $cities = \App\ChainCity::
                $cities = \App\City::
                select('cities.id','cities.name'
//                    \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as name')
                )
//                ->join('cities','cities.id', 'chain_city.city_id')
                ->orderBy('cities.name')
                ->whereNotIn('cities.id',[1,3,5,7,9,2,4,6,8,10])
                ->pluck('cities.id')
                ->toArray();
        $cityIds = array_merge([1,3,5,7,9,2,4,6,8,10],$cityIds);
//        print_r($cityIds);exit;
//        $cities = \App\ChainCity::
        $cities = \App\City::
                select('cities.id','cities.name'
//                    \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as name')
                )
//                ->join('cities','cities.id', 'chain_city.city_id')
                ->orderByRaw('FIELD (cities.id, ' . implode(', ', $cityIds) . ')')
                ->get();
        $items = [];
        foreach ($cities as $key=>$city)
        {
            if($key == 5)
            {
                $items[] = [
                    'divider' => true
                ];
            }
            
            $items[] = $city;
        }
        $regions = collect($items);
//        $regions = RegionRepository::all();

//        print_r(json_encode(\Auth::user()->company->appendProfileData()->toArray()));exit;
        return view('pages.profile', compact('languages', 'property_types', 'regions', 'services','becamebroker'));
    }

    public function update(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }
        
        if (Auth::user()->isBroker()) {
            BrokerRepository::update($request->all(), Auth::user());
            Cache::forget(Auth::user()->company->id . ':spheres');
        } else {
            if($request->becamebroker){
                BrokerRepository::create($request->all());
            }else{
                (new UserRepository())->update(Auth::user(), $request->all());
            }
        }

        return response()->json([
            'redirect' => route('profile.me'),
            'message' => 'Vartotojas sėkmingai atnaujintas!',
        ], 200);
    }
}
