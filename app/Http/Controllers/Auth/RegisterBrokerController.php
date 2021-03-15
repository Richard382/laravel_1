<?php

namespace App\Http\Controllers\Auth;

use App\PropertyType;
use App\Repositories\BrokerRepository;
use App\Repositories\CityRepository;
use App\Language;
use App\Repositories\RegionRepository;
use App\Service;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RegisterBrokerController extends RegisterController
{
    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'              => ['required', 'string', 'max:255'],
            'agency'            => ['required', 'string', 'max:255'],
            'paths'             => ['required', 'array'],
            'language'          => ['required', 'array', 'exists:languages,id'],
            'description'       => ['required', 'string'],
            'phone'             => ['required', 'regex:/^(\+370)\d{8}$/i'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'website'           => ['max:255'],
            'represent_video'   => ['nullable', 'url', 'max:255'],
            'avatar'            => ['imageable'],
            'company_logo'      => ['imageable'],
            'certificates.*'    => ['image'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'agreement'         => ['required']
        ];

        // Validate paths
        if (isset($data['paths'])) {
            foreach ($data['paths'] as $key => $path) {
                $rules['paths.' . $key . '.property_type']      = ['required', 'exists:property_types,id'];
                $rules['paths.' . $key . '.properties']         = ['required', 'exists:properties,id'];
                $rules['paths.' . $key . '.regions.*']            = ['required', 'exists:regions,id'];
                $rules['paths.' . $key . '.services.*']           = ['required', 'exists:services,id'];
//                $rules['paths.' . $key . '.price_from']         = ['lt:' . 'paths.' . $key . '.price_to'];
//                $rules['paths.' . $key . '.price_to']           = [];
            }
        }

        return Validator::make($data, $rules, [
            'email.unique' => 'Šis el. paštas jau yra naudojamas.'
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        event(new Registered(self::create($request->all())));

        return response()->json([
            'redirect' => route('login'),
            'message' => 'Vartotojas sėkmingai sukurta! Netrukus gausite el. laišką iš CONT ( patikrinkite Spam aplanką) ir patvirtinkite savo paskyrą.',
        ], 200);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $languages = Language::all();

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
//        print_r($regions->toArray());exit;

        return view('auth.broker.register', compact('languages', 'property_types', 'regions', 'services'));
    }

    /**
     * @param array $data
     * @return \App\User|void
     */
    public function create(array $data)
    {
        $user = BrokerRepository::create($data);

        return $user;
    }
}
