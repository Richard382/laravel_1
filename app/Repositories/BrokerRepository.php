<?php

namespace App\Repositories;

use App\Company;
use App\Property;
use App\PropertyType;
use App\User;
use Illuminate\Support\Facades\Hash;

class BrokerRepository
{
    public static function create(array $data)
    {
        if(!\Auth::check()) {
            $user = (new UserRepository())->create($data);
        }else{
            $user = \Auth::user();
            $user = (new UserRepository())->update($user, $data);
        }

        // Assing usr role
        if(!$user->isBroker()) {
            $user->become('broker');
        }
        $company_data = [
            'name' => $data['agency'],
            'description' => $data['description']
        ];

        if (isset($data['represent_video'])) {
            $company_data['represent_video'] = $data['represent_video'];
        }

        if (isset($data['website'])) {
            $company_data['website'] = $data['website'];
        }

        if (isset($data['company_logo'])) {
            $company_data['avatar'] = $data['company_logo'];
        }

        // Create company
        $company = Company::create($company_data);

        // Associate user to company
        $user->company()->associate($company);
        $user->save();

        $company->languages()->sync($data['language']);

        if (isset($data['certificates'])) {
            foreach ($data['certificates'] as $certificate) {
                $company->certificates()->create([
                    'url' => $certificate->store('certificates', 'public')
                ]);
            }
        }

        $company->save();

        foreach ($data['paths'] as $path)
        {
            // Create sphere
            $sphere = $company
                ->spheres()
                ->create([
                    'price_from' => isset($path['price_from']) ? $path['price_from'] : null,
                    'price_to' => isset($path['price_to']) ? $path['price_to'] : null,
                ]);

            // Set property type
            $sphere->type()->associate(PropertyType::find($path['property_type']));

            // Sync Sphere properties
            $sphere->property()->associate(Property::find($path['properties']));

            // Save associates
            $sphere->save();

            // Sync Sphere cities
//            $sphere
//                ->regions()
//                ->sync($path['regions']);
            

            $sphere
                ->services()
                ->sync($path['services']);
            
            $sphereCityArr = [];
            \DB::table('region_sphere')->where('sphere_id',$sphere->id)->delete();
            $cities = \App\City::whereIn('id',$path['regions'])
                    ->get();    
            foreach($cities as $city){
                $sphereCityArr[] = [
                    'sphere_id' => $sphere->id,
                    'region_id' => $city->region_id,
                    'city_id' => $city->id
                ];
            }
            \DB::table('region_sphere')->insert($sphereCityArr);
            
//            try { // create chain
                
//                $chainCities = json_encode($path['regions']);
//                $chainProperty_types = json_encode([$path['property_type']]);
//                $chainProperties = json_encode([$path['properties']]);
//                $chainServices = json_encode($path['services']);
//                $isExist = \App\Chain::where([
//                    'cities' => $chainCities,
//                    'propertytypes' => $chainProperty_types,
//                    'properties' => $chainProperties,
//                    'services' => $chainServices,
//                    'user_id' => $user->id
//                ])->first();
//                if(!$isExist) {
//                    $chain = \App\Chain::create([
//                        'usertype' => 'provider',
//                        'price_from' => $path['price_from'],
//                        'price_to' => $path['price_to'],
//                        'user_id' => $user->id,
//                        'cities' => $chainCities,
//                        'propertytypes' => $chainProperty_types,
//                        'properties' => $chainProperties,
//                        'services' => $chainServices,
//                    ]);
//                }
//            }  catch (\Exception $e){}
        }

        return $user;
    }

    public static function update(array $data, User $user)
    {
        $user = (new UserRepository())->update($user, $data);

        $company_data = [
            'name' => $data['agency'],
            'description' => $data['description']
        ];

        if (isset($data['represent_video'])) {
            $company_data['represent_video'] = $data['represent_video'];
        }

        if (isset($data['website'])) {
            $company_data['website'] = $data['website'];
        }

        if (isset($data['company_logo'])) {
            $company_data['avatar'] = $data['company_logo'];
        }

        // Create company
        $user->company->update($company_data);

        $user->company->languages()->sync($data['language']);

        $user->company->spheres()->delete();

        if (isset($data['certificates'])) {
            $user->company->certificates()->delete();

            foreach ($data['certificates'] as $certificate) {
                $user->company->certificates()->create([
                    'url' => $certificate->store('certificates', 'public')
                ]);
            }
        }

        foreach ($data['paths'] as $path)
        {
            // Create sphere
            $sphere = $user->company
                ->spheres()
                ->create([
                    'price_from' => $path['price_from'],
                    'price_to' => $path['price_to']
                ]);

            // Set property type
            $sphere->type()->associate(PropertyType::find($path['property_type']));

            // Sync Sphere properties
            $sphere->property()->associate(Property::find($path['properties']));

            // Save associates
            $sphere->save();

            // Sync Sphere cities
//            $sphere
//                ->regions()
//                ->sync($path['regions']);

            $sphere
                ->services()
                ->sync($path['services']);
            
            $sphereCityArr = [];
            \DB::table('region_sphere')->where('sphere_id',$sphere->id)->delete();
            $cities = \App\City::whereIn('id',$path['regions'])
                    ->get();    
            foreach($cities as $city){
                $sphereCityArr[] = [
                    'sphere_id' => $sphere->id,
                    'region_id' => $city->region_id,
                    'city_id' => $city->id
                ];
            }
            \DB::table('region_sphere')->insert($sphereCityArr);
            
            try { // create chain
                
//                $chainCities = json_encode($path['regions']);
//                $chainProperty_types = json_encode([$path['property_type']]);
//                $chainProperties = json_encode([$path['properties']]);
//                $chainServices = json_encode($path['services']);
//                $isExist = \App\Chain::where([
//                    'cities' => $chainCities,
//                    'propertytypes' => $chainProperty_types,
//                    'properties' => $chainProperties,
//                    'services' => $chainServices,
//                    'user_id' => $user->id
//                ])->first();
//                if(!$isExist) {
//                    $chain = \App\Chain::create([
//                        'usertype' => 'provider',
//                        'price_from' => $path['price_from'],
//                        'price_to' => $path['price_to'],
//                        'user_id' => $user->id,
//                        'cities' => $chainCities,
//                        'propertytypes' => $chainProperty_types,
//                        'properties' => $chainProperties,
//                        'services' => $chainServices,
//                    ]);
//                }

//                if($chain) {
//                    $insertServices = [];
//                    foreach($path['services'] as $service){
//                        $insertServices[] = [ 'chain_id' => $chain->id, 'service_id' => $service ];
//                    }
//                    $insertCity = [];
//                    foreach($path['regions'] as $cityid){
//                        $insertCity[] = [ 'chain_id' => $chain->id, 'city_id' => $cityid ];
//                    }
//                    
//                    \App\ChainService::insert($insertServices);
//                    \App\ChainCity::insert($insertCity);
//
//                    \App\ChainProperty::create([
//                        'chain_id' => $chain->id, 'property_id' => $path['properties']
//                    ]);
//
//                    \App\ChainPropertyType::create([ 
//                        'chain_id' => $chain->id, 'property_type_id' => $path['property_type'] 
//                    ]);
//                }
            }  catch (\Exception $e){}
        }
        
        return $user;
    }
}
