<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chain;

class ChainController extends Controller
{
    public function index(Request $req) {
        return view('admin.chain');
    }
    
    public function getChainData(Request $req) {
        $data = [];
        $customerChain = [];
        
        
//        foreach($chainService as $key=>$service){
            $propertyTypes = \App\ChainPropertyType::select('chain_property_type.id', 'chain_property_type.property_type_id',
                    \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as cust_name'),
                    \DB::raw('if(property_types.provider_display_name = "", property_types.name, property_types.provider_display_name) as prov_name')
                )
                ->join('property_types', 'property_types.id', 'chain_property_type.property_type_id')
//                ->where('chain_property_type.service_id',$service['chainserviceid'])
                ->get();
            foreach($propertyTypes as $key2=>$propertyType){
                $properties = \App\ChainProperty::select('chain_properties.id', 'chain_properties.property_id', 'properties.pricetype',
                        \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as cust_name'),
                        \DB::raw('if(properties.provider_display_name = "", properties.name, properties.provider_display_name) as prov_name')
                        )
                    ->join('properties', 'properties.id', 'chain_properties.property_id')
                    ->where('chain_properties.chain_property_type_id', $propertyType['id'])
                    ->get();
                foreach($properties as $key3=>$property){
                    $services = \App\ChainService::select('chain_service.id','chain_service.service_id','chain_service.provider_fee',
                            'services.pricetype','services.fixedlocation',
                            \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as cust_name'),
                            \DB::raw('if(services.provider_display_name = "", services.type_broker, services.provider_display_name) as prov_name')
                            )
                        ->join('services', 'chain_service.service_id', 'services.id')
                        ->where('chain_service.chain_property_id', $property['id'])
                        ->get();
                    $properties[$key3]['services'] = $services;
                }
                $propertyTypes[$key2]['properties'] = $properties;
            }
//            $chainService[$key]['propertytype'] = $propertyTypes;
//        }
//        $customerChain['customer']['services'] = $propertyTypes;
//        print_r($propertyTypes);exit;
        $customerChain = $propertyTypes;
        $customerCities = \App\ChainCity::select('chain_city.id','chain_city.city_id',
                    \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as cust_name'),
                    \DB::raw('if(cities.provider_display_name = "", cities.name, cities.provider_display_name) as prov_name')
                )
                ->join('cities','cities.id', 'chain_city.city_id')
                ->get();
//        $customerChain['customer']['properties'] = [];
//        $customerChain['customer']['cities'] = [];
//        $customerChain = Chain::
//                get()->groupBy('usertype');
//        foreach($customerChain as $chainUsertype) {
//            foreach($chainUsertype as $chain) {
//                $serviceids = json_decode($chain->services,true);
//                $propertytypes = json_decode($chain->propertytypes,true);
//                $properties = json_decode($chain->properties,true);
//                $cities = json_decode($chain->cities,true);
//                $chain['services'] = \App\Service::whereIn('id',$serviceids)->get();
//                $chain['propertytypes'] = \App\PropertyType::whereIn('id',$propertytypes)->get();
//                $chain['properties'] = \App\Property::whereIn('id',$properties)->get();
//                $chain['cities'] = \App\City::whereIn('id',$cities)->get();
//            }
//        }
//        if(!isset($customerChain['customer'])){
//            $customerChain['customer'] = [];
//        }
//        if(!isset($customerChain['provider'])){
//            $customerChain['provider'] = [];
//        }
//        $customerChain
        $data['services'] = \App\Service::select('id','provider_fee',
                    \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as cust_name'),
                    \DB::raw('if(services.provider_display_name = "", services.type_broker, services.provider_display_name) as prov_name')
                )
                ->get();
        $data['propertytypes'] = \App\PropertyType::select('id',
                    \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as cust_name'),
                    \DB::raw('if(property_types.provider_display_name = "", property_types.name, property_types.provider_display_name) as prov_name')
                )
                ->get();
//        print_r($data['propertytypes']);exit;
        $data['properties'] = \App\Property::select('id','pricetype',
                    \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as cust_name'),
                    \DB::raw('if(properties.provider_display_name = "", properties.name, properties.provider_display_name) as prov_name')
                )
                ->get();
        $data['cities'] = \App\City::select('id',
                    \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as cust_name'),
                    \DB::raw('if(cities.provider_display_name = "", cities.name, cities.provider_display_name) as prov_name')
                )->get();
//        print_r($customerChain->toArray());exit;
        return json_encode(['chainpropertytypes' => $customerChain, 'customerCities' => $customerCities,'data' => $data]);
//        return view('admin.chain',  compact('customerChain','customerCities','data'));
    }
    
    public function save(Request $req) {
//        print_r($req->data);exit;
        try {
            \App\ChainPropertyType::get()->each->delete();
            \App\ChainProperty::get()->each->delete();
            \App\ChainService::get()->each->delete();
            \App\ChainCity::get()->each->delete();
            
            foreach($req->data as $propertytype){
               $newPropertyType = \App\ChainPropertyType::create([
                   'property_type_id' => $propertytype['property_type_id'], 
               ]);
               \App\PropertyType::where('id',$propertytype['property_type_id'])
                    ->update(['customer_display_name' => $propertytype['cust_name'],
                    'provider_display_name' => $propertytype['prov_name']]);
               if(isset($propertytype['properties'])){
                    foreach($propertytype['properties'] as $property) {
                        $newProperty = \App\ChainProperty::create([
                            'chain_property_type_id' => $newPropertyType->id,
                            'property_id' => $property['property_id'],
                        ]);
                        \App\Property::where('id',$property['property_id'])
                            ->update(['customer_display_name' => $property['cust_name'],
                                'provider_display_name' => $property['prov_name'],
                                'pricetype' => $property['pricetype'],
                            ]);
                        if(isset($property['services'])){
                            foreach($property['services'] as $service) {
                                $newService = \App\ChainService::create([
                                    'chain_property_id' => $newProperty->id,
                                    'service_id' => $service['service_id'],
                                    'provider_fee' => $service['provider_fee'],
                                ]);
                                \App\Service::where('id',$service['service_id'])
                                        
                                ->update(['customer_display_name' => $service['cust_name'],
                                    'provider_display_name' => $service['prov_name'],
                                    'pricetype' => isset($service['pricetype'])?$service['pricetype']:"range",
                                    'fixedlocation' => isset($service['fixedlocation']) && $service['fixedlocation']?"1":"0",
                                ]);
                            }
                        }
                    }
               }
            }
//            if($req->locations) {
//                foreach($req->locations as $location){
//                    \App\ChainCity::create([
//                       'city_id' => $location['city_id']
//                    ]);
//                    \App\City::where('id',$location['city_id'])
//                        ->update(['customer_display_name' => $location['cust_name'],
//                        'provider_display_name' => $location['prov_name']]);
//                }
//            }
            return json_encode(['success'=>true]);
        } catch(\Exception $e) {
            return json_encode(['success'=>false,'error'=>$e->getLine().$e->getMessage()]);
        }
    }
    
    public function createPropertyType(Request $req){
        try {
            $cust_name = $req->cust_name;
            $prov_name = $req->prov_name;
            
            $itemCreate = new \App\PropertyType();
            $itemCreate->name = $cust_name;
            $itemCreate->customer_display_name = $cust_name;
            $itemCreate->service_id = '1';
            $itemCreate->provider_display_name = $prov_name;
            $isCreated = $itemCreate->save();
            
            if($isCreated) {
                $data = \App\PropertyType::select('id',
                    \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as cust_name'),
                    \DB::raw('if(property_types.provider_display_name = "", property_types.name, property_types.provider_display_name) as prov_name')
                )
                ->where('id',$itemCreate->id)->first();
                return json_encode(['success'=>true,'data'=>$data]);
            }
            return json_encode(['success'=>false]);
        } catch(\Exception $e) {
            return json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
    }
    
    public function createProperty(Request $req){
        try {
            $cust_name = $req->cust_name;
            $prov_name = $req->prov_name;
            
            $itemCreate = new \App\Property();
            $itemCreate->name = $cust_name;
            $itemCreate->customer_display_name = $cust_name;
            $itemCreate->property_type_id = '1';
            $itemCreate->provider_display_name = $prov_name;
            $isCreated = $itemCreate->save();
            
            if($isCreated) {
                $data = \App\Property::select('id','pricetype',
                    \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as cust_name'),
                    \DB::raw('if(properties.provider_display_name = "", properties.name, properties.provider_display_name) as prov_name')
                )
                ->where('id',$itemCreate->id)->first();
                return json_encode(['success'=>true,'data'=>$data]);
            }
            return json_encode(['success'=>false]);
        } catch(\Exception $e) {
            return json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
    }
    public function createService(Request $req){
        try {
            $cust_name = $req->cust_name;
            $prov_name = $req->prov_name;
            
            $itemCreate = new \App\Service();
            $itemCreate->type_user = $cust_name;
            $itemCreate->type_broker = $prov_name;
            $itemCreate->type = 'other';
            $itemCreate->customer_display_name = $cust_name;
            $itemCreate->provider_display_name = $prov_name;
            $isCreated = $itemCreate->save();
            
            if($isCreated) {
                $data = \App\Service::select('id','provider_fee',
                    \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as cust_name'),
                    \DB::raw('if(services.provider_display_name = "", services.type_broker, services.provider_display_name) as prov_name')
                )
                ->where('id',$itemCreate->id)->first();
                return json_encode(['success'=>true,'data'=>$data]);
            }
            return json_encode(['success'=>false]);
        } catch(\Exception $e) {
            return json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
    }
    
    public function createLocation(Request $req) {
        try {
            $cust_name = $req->cust_name;
            $prov_name = $req->prov_name;
            
            $itemCreate = new \App\City();
            $itemCreate->name = $cust_name;
            $itemCreate->customer_display_name = $cust_name;
            $itemCreate->provider_display_name = $prov_name;
            $isCreated = $itemCreate->save();
            
            if($isCreated) {
                $data = \App\City::select('id',
                    \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as cust_name'),
                    \DB::raw('if(cities.provider_display_name = "", cities.name, cities.provider_display_name) as prov_name')
                )->where('id', $itemCreate->id)->first();
                return json_encode(['success'=>true,'data'=>$data]);
            }
            return json_encode(['success'=>false]);
        } catch(\Exception $e) {
            return json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
    }

}
