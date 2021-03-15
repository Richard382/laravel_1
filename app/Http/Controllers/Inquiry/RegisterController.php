<?php

namespace App\Http\Controllers\Inquiry;

use App\City;
use App\Company;
use App\Events\NewInquiry;
use App\Helpers\Token;
use App\Http\Controllers\Company\ContactController;
use App\Inquiry;
use App\Property;
use App\PropertyType;
use App\Repositories\CityRepository;
use App\Repositories\InquiryRepository;
use App\Repositories\UserRepository;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class RegisterController extends \App\Http\Controllers\Controller
{
    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Base validation for Authenticated
        $validation_data = [
            'requirements' => ['required', 'string', 'max:300'],
            'location.*' => ['required', 'exists:cities,id', 'integer'],
            'in_hurry' => ['boolean'],
            'agreement' => ['required', 'boolean'],
            'property_type.*' => ['required', 'exists:property_types,id', 'integer'],
            'properties.*' => ['required', 'exists:properties,id', 'integer'],
            'service' => ['required', 'exists:services,id'],
        ];

        if (isset($data['contact_company']) && $data['contact_company']) {
            $validation_data['contact_company'] = 'exists:companies,id';
        }

        // If not logged in add extra rules
        if ( ! Auth::check()) {
            $validation_data = Arr::collapse([$validation_data, [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'regex:/^(\+370)\d{8}$/i'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'register' => ['boolean'],
                'password' => ['required_if:register,1', 'string', 'min:8', 'confirmed'],
            ]]);

            if (isset($data['register'])) {
                $validation_data['email'][] = 'unique:users';
            }
        }

        return Validator::make($data, $validation_data, [
            'email.unique' => 'Šis el. paštas jau yra naudojamas.'
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $property_types_pre = false;
        $properties_pre     = false;
        $service            = false;

        if ($request->has('property_type')) {
            $property_types_pre = PropertyType::
                     select("property_types.*",
                        \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as name')
                    )   
                    ->whereIn('id', $request->get('property_type'))->get();
        }
            
        if ($request->has('properties')) {
            $properties_pre = Property::
                    select("properties.*",
                        \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as name')
                    )
                    ->whereIn('id', $request->get('properties'))->get();
        }

        if ($request->has('service')) {
            $service = Service::where("id",$request->get('service'))
                    ->select("services.*",
                        \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as name'),
                        \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as type_user')
                    )->first();
        }

        $cityIds = 
                $cities = \App\City::
                select('cities.id','cities.name'
                )
                ->orderBy('cities.name')
                ->whereNotIn('cities.id',[1,3,5,7,9,2,4,6,8,10])
                ->pluck('cities.id')
                ->toArray();
        $cityIds = array_merge([1,3,5,7,9,2,4,6,8,10],$cityIds);
        $cities = \App\City::
                select('cities.id','cities.name'
                )
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
        $cities = collect($items);

        $services = Service::
                    select("services.*",\DB::raw("10 as price"),
                        \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as name'),
                        \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as type_user')
                    )->get()
                ;

        $property_types = PropertyType::select("property_types.*",
                \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as name')
                )
                ->with('properties')->get();

        return view('inquiries.create', [
            'cities' => $cities,
            'service' => $service,
            'price_from' => $request->get('price_from', false),
            'price_to' => $request->get('price_to', false),
            'location' => $request->get('location'),
            'property_types_pre' => $property_types_pre,
            'properties_pre' => $properties_pre,
            'services' => $services,
            'property_types' => $property_types
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        $inquiry = $this->create($request->all());

        // Add redirect which redirects visitor to tokenized link and authenticated to authenticated
        return response()->json([
            'redirect' => $inquiry->getLink() . '?fresh=1',
            'message' => $inquiry->active ?
                'Užklausa sėkmingai sukurta!' :
                'Vartotojas sėkmingai sukurta! Netrukus gausite el. laišką iš CONT (patikrinkite brukalo aplanką) ir patvirtinkite savo paskyrą, tik patvirtinus el.paštą galėsite matyti skelbimą!',
        ], 201);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    private function create(array $data)
    {
        $inquiry_data = [
            'requirements' => $data['requirements'],
            'price_from' => isset($data['price_from']) && $data['price_from'] ? $data['price_from'] : 0,
            'price_to' => isset($data['price_to']) && $data['price_to'] ? $data['price_to'] : 0,
            'in_hurry' => Arr::get($data, 'in_hurry', false),
            'property_types' => $data['property_type'],
            'properties' => $data['properties'],
            'service_id' => $data['service'],
            'location' => $data['location'],
            'active' => 1
        ];

        // Check if user is logged in OR person is willing to register
        if (Auth::check() || Arr::get($data, 'register', false))
        {
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = (new UserRepository())->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => $data['password']
                ]);

                $user->become('regular');

                $inquiry_data['active'] = 0;

                if (isset($data['contact_company']) && $data['contact_company']) {
                    $inquiry_data['contact_company'] = $data['contact_company'];
                }
            }

            $inquiry_data = Arr::collapse([$inquiry_data, [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]]);

        } else {
            $inquiry_data = Arr::collapse([$inquiry_data, [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'token' => Token::generate()
            ]]);
        }

        $inquiry = InquiryRepository::create($inquiry_data);

        if ($inquiry_data['active']) {
            event(new NewInquiry($inquiry));

            if (isset($data['contact_company']) && $data['contact_company']) {
                $comapny = Company::find($data['contact_company']);

                $response = ContactController::connect($comapny, $inquiry);

                if ($response->getStatusCode() !== 200) {
                    return $response;
                }
            }
        }

        return $inquiry;
    }
}
