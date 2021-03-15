<?php

namespace App\Http\Controllers;

use App\PropertyType;
use App\Repositories\CityRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\InquiryRepository;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\User;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        try{
 
            $property_types = \App\ChainPropertyType::select('property_types.id','chain_property_type.id as chain_property_type_id',
                    \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as name')
                )
                ->join('property_types', 'property_types.id', 'chain_property_type.property_type_id')
                ->get();
            foreach($property_types as $key2=>$propertyType){
                $properties = \App\ChainProperty::select('properties.id','properties.pricetype',
                        \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as name')
                        )
                    ->join('properties', 'properties.id', 'chain_properties.property_id')
                    ->where('chain_properties.chain_property_type_id', $propertyType['chain_property_type_id'])
                    ->get();
                $property_types[$key2]['properties'] = $properties;
            }
        $services = \App\ChainService::select('services.id','services.provider_fee',
                'services.pricetype','services.fixedlocation',
            \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as name')
            )
            ->join('services','services.id','chain_service.service_id')
            ->get();
        
        foreach($services as $service){
            if($price = \App\Price::where('service',$service->id)->first()){
                $service->price = $price->price;
            }
        }
        
        $companies = CompanyRepository::top();
        
        $cityIds = 
                $cities = \App\City::
                select('cities.id','cities.name'
                )
                ->orderBy('cities.name')
                ->whereNotIn('cities.id', [1,3,5,7,9,2,4,6,8,10])
                ->pluck('cities.id')
                ->toArray();
        $cityIds = array_merge([1,3,5,7,9,2,4,6,8,10],$cityIds);

        $inquiries = $this->getHomeInquiries();
        $cities = \App\City::
                select('cities.id','cities.name'
                )
                ->orderByRaw('FIELD (cities.id, ' . implode(', ', $cityIds) . ')')
                ->get();
        $items = [];
        foreach ($cities as $key=>$city)
        {
            // if($key == 5 || $key == 10)
            // {
            //     $items[] = [
            //         'divider' => true
            //     ];
            // }
            
            $items[] = $city;
        }
        $cities = collect($items);
        
        $preset_city = $request->has('location') ? collect($request->get('location'))->toJson() : false;
        $preset_service = $request->get('service', false);
        $preset_property_type = $request->has('property_type') ? collect($request->get('property_type'))->toJson() : false;
        $preset_properties = $request->has('properties') ? collect($request->get('properties'))->toJson() : false;
        $preset_price_from = $request->get('price_from', false);
        $preset_price_to = $request->get('price_to', false);
// dd($preset_property_type);
        return view('home', compact(
            'cities',
            'preset_service',
            'preset_price_from',
            'preset_price_to',
            'preset_property_type',
            'property_types',
            'services',
            'companies',
            'preset_city',
            'inquiries',
            'preset_properties'
            ));
        }
        catch(\Exception $e){
            return $e->getLine().$e->getMessage();
        }
    }
    public function getNewInquiries(){
        $temp_inquiries = $this->getHomeInquiries();
        foreach($temp_inquiries as $inquiry) {
            // dd($inquiry->property_type[0]->name);
            $region = substr_count($inquiry->sum_up, ',');
            if($region == 58) {
                $t_inquiry = $inquiry->toArray();
                $t_inquiry['sum_up'] = "Visa Lietuva";
                $inquiries[] = $t_inquiry;
            }else {
                $inquiries[] = $inquiry;
            }
        }
        
        return response()->json(['success'=>true,'data'=>$inquiries]);
    }
    public function getNewInquiriestype(){
        $inquiries = $this->getHomeInquiries();
        $lookingfor = $offers = $other = [];
        foreach($inquiries as $temp) {
            if($temp->service_id == 1)
                $lookingfor = $temp;
            elseif($temp->service_id == 2)
                $offers = $temp;
            elseif($temp->service_id == 3)
                $other  = $temp;
        }
        $newinquiries = [$lookingfor, $offers, $other];
        return response()->json(['success'=>true,'data'=>$newinquiries]);
    }
    public function getHomeInquiries(){
        $inquiries = [];
        if(!\Auth::check()){
            $inquiries = InquiryRepository::latestAll();
        }else if(\Auth::user()->isBroker()){
        
            $inquiries = InquiryRepository::getBroker(false, false);
            $testInq = clone $inquiries;
            $testInq = $testInq->get();
            $testInqIds = [];
            $sphereData = $this->checkProvideSphereData(\Auth::user());
            $testInqIds = $this->checkProvideHasMatchChain2($testInq,$sphereData);
            $inquiries = $inquiries->whereIn('inquiries.id',$testInqIds);
            $inquiries = $inquiries->limit(4)
            ->orderBy('id','desc')
            ->get();
            
            foreach($inquiries as $item){
                $hasPrice = \App\Price::where('service',$item->service_id)->first();
                if($hasPrice){
                    $item->offer_price = \App\Helpers\Shop::formatPrice($hasPrice->price);
                }
                if(isset($item->properties) && $item->properties && count($item->properties)){
                    $propid = $item->properties[0]['id'];
                    $getProp = \App\ChainProperty::select('chain_service.provider_fee')
                        ->where('property_id',$propid)
                        ->join('chain_service','chain_service.chain_property_id','chain_properties.id')
                        ->where('chain_service.service_id',$item->service_id)
                        ->first();
                    if($getProp && (float)$getProp->provider_fee > 0){
                        $item->offer_price = \App\Helpers\Shop::formatPrice($getProp->provider_fee);
                    }
                }
            }
//
//            $inquiriesss = new \Illuminate\Pagination\LengthAwarePaginator(
//                $itemsTransformed,
//                $inquiries->total(),
//                $inquiries->perPage(),
//                $inquiries->currentPage(), [
//                    'path' => \Request::url(),
//                    'query' => [
//                        'page' => $inquiries->currentPage()
//                    ]
//                ]
//            );
//            $inquiries = $inquiriesss;
        }else{
            $inquiries = \App\Inquiry::where('user_id', \Auth::check() ? \Auth::user()->id : 0)
                ->where(function($query) {
                $query->whereDoesntHave('archived', function ($query) {
                    $query->where('user_id', '=', \Auth::user()->id);
                })
                ->orWhereDoesntHave('archived');
            })->limit(4)
            ->orderBy('id','desc')->get();
//                    getRegular();
        }
        return $inquiries;
    }
    public function checkProvideSphereData($user) {
        $SphereArr = [];
        $companyid = $user->company_id;
        $spheres = \App\Sphere::where('company_id', $companyid)
                ->with('regions')
                ->with('services')
                ->get();
        foreach($spheres as $sphere){
            $serviceIds = [];
            $regionIds = [];
            $spherepricefrom = (double) $sphere->price_from;
            $spherepriceto = (double) $sphere->price_to;

            foreach ($sphere->services as $service2){
                $serviceIds[] = $service2['id'];
            }
            
            $regionIds = \DB::table('region_sphere')->where('sphere_id',$sphere->id)->pluck('city_id')->toArray();
//            foreach ($sphere->regions as $region){
//                $regionIds[] = $region['id'];
//            }

            $SphereArr[] = [
                'services' => $serviceIds,
                'propertytype' => $sphere->property_type_id,
                'property' => $sphere->property_id,
                'region' => $regionIds,
                'pricefrom' => $spherepricefrom,
                'priceto' => $spherepriceto
            ];
        }
        return $SphereArr;
    }
    public function checkProvideHasMatchChain2($inquiries, $spheres) {
        try{
            $inqIds = [];
            foreach($inquiries as $inquiry) {
                $propertyTypes = [];
                $properties = [];
                $service = $inquiry->service_id;
                $inqprice_from = (double) $inquiry->price_from;
                $inqprice_to = (double) $inquiry->price_to;


                $cities = [];
                foreach($inquiry->property_type as $data){
                    $propertyTypes[] = $data['id'];
                }
                foreach($inquiry->properties as $data){
                    $properties[] = $data['id'];
                }
                foreach($inquiry->cities as $data){
                    $cities[] = $data['id'];
                }
                foreach($spheres as $sphere){
                    $spherepricefrom = $sphere['pricefrom'];
                    $spherepriceto = $sphere['priceto'];

                    if(in_array($sphere['propertytype'], $propertyTypes)
                        && in_array($sphere['property'], $properties)
                        && in_array($service, $sphere['services'])
                        && count(array_intersect($cities, $sphere['region']))
                        && ($inqprice_from < 1 || $inqprice_from <= $spherepriceto)
                        && ($inqprice_to < 1 || $inqprice_to >= $spherepricefrom)
                    ) {
                        $inqIds[] = $inquiry->id;
                        break;
                    }
                }
            }
            return $inqIds;
        }catch(\Exception $e){
            return false;
        }
    }
    public function checkProvideHasMatchChain($inquiry, $user) {
//            cmp = 23, sphere = 36, type = 1 , prop = 1, service = 1,2,3,4, region = 1,2,3,4
            $inquiry = \App\Inquiry::where('id',$inquiry['id'])
                ->with('property_type')
                ->with('properties')
                ->with('service')
                ->with('cities')
                ->first();
                
                dd($inquiry);
            if(!$inquiry){ return false;}
            $companyid = $user->company_id;
            $propertyTypes = [];
            $properties = [];
            $service = $inquiry->service_id;
            $inqprice_from = (double) $inquiry->price_from;
            $inqprice_to = (double) $inquiry->price_to;
            
            
            $cities = [];
            foreach($inquiry->property_type as $data){
                $propertyTypes[] = $data['id'];
            }
            foreach($inquiry->properties as $data){
                $properties[] = $data['id'];
            }
            foreach($inquiry->cities as $data){
                $cities[] = $data['region_id'];
            }
 
            $spheres = \App\Sphere::where('company_id', $companyid)
                    ->with('regions')
                    ->with('services')
                    ->get();
            foreach($spheres as $sphere){
                $serviceIds = [];
                $regionIds = [];
                $spherepricefrom = (double) $sphere->price_from;
                $spherepriceto = (double) $sphere->price_to;

                foreach ($sphere->services as $service2){
                    $serviceIds[] = $service2['id'];
                }
                foreach ($sphere->regions as $region){
                    $regionIds[] = $region['id'];
                }
 
                if(in_array($sphere->property_type_id, $propertyTypes)
                    && in_array($sphere->property_id, $properties)
                    && in_array($service, $serviceIds)
                    && count(array_intersect($cities, $regionIds))
                    && $inqprice_from >= $spherepricefrom
                    && $inqprice_from < $spherepriceto
                    && ($inqprice_to < 1 || $inqprice_to <= $spherepriceto)
                ){
                    return true;
                }
            }
            return false;
 
            
            $providerChains = \App\Chain::where('user_id',$userid)->get();
            $inqIdArr = [];
            foreach ($providerChains as $chain) {
                $services = json_decode($chain->services, true);
                $property_type = json_decode($chain->propertytypes, true);
                $properties = json_decode($chain->properties, true);
                $cities = json_decode($chain->cities, true);
                $price_from = $chain->price_from;
                $price_to = $chain->price_to;
                $inq = $inquiry->toArray();

                $checks = [
                    'inqid' => $inq['id'],
                    'property_type' => array_column($inq['property_type'], 'id'),
                    'properties' => array_column($inq['properties'], 'id'),
                    'cities' => array_column($inq['cities'], 'id'),
                    'services' => [$inq['service_id']]
                ];
                $match1 = count(array_intersect($checks['property_type'], $property_type)) == count($checks['property_type']) && !empty($checks['property_type']);
                $match2 = count(array_intersect($checks['properties'], $properties)) == count($checks['properties']) && !empty($checks['properties']);
                $match3 = count(array_intersect($checks['cities'], $cities)) == count($checks['cities']) && !empty($checks['cities']);
                $match4 = count(array_intersect($checks['services'], $services)) == count($checks['services']) && !empty($checks['services']);
                if($match1 && $match2 && $match3 && $match4 && ($inq['price_from'] >= $price_from && $inq['price_to'] <= $price_to)){
                    return true;
                }
            }
            return false;
       
    }
    
    public function getspecialNT(Request $req) {
        try{
            $inquiries = $this->getHomeInquiries();
            $data['services'] =  $req->service;
            $data['property'] =   $req->properties[0];
            $data['propertytype'] = $req->property_type[0];
            $data['region'] = $req->location;
            $data['pricefrom'] = floatval($req->price_from);
            $data['priceto'] = floatval($req->price_to);
            $result_data[] = $data;
            $user_list = User::all();
            $i = 0;
            $count = count($this->checkProvideHasMatchChain2($inquiries, $result_data));
            return response()->json(['success'=>true,'data'=>$count]);
        }catch(\Exception $e) {
            
        }
        
    }
}
