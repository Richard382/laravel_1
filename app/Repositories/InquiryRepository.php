<?php

namespace App\Repositories;

use App\Inquiry;
use App\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InquiryRepository
{
    public static function getRegular($archived = false)
    {
        $q_inqueries = Inquiry::owns();

        if ($archived) {
            $q_inqueries = $q_inqueries->archived();
        } else {
            $q_inqueries = $q_inqueries->notArchived();
        }

        return $q_inqueries;
    }

    public static function getBroker($archived = false, $onlyOffered = false, $orderBy = true)
    {
        $spheres = Auth::user()->getCompany()->getSpheres();

        $q_inqueries = Inquiry::notMine();

        if ($archived) {
            $q_inqueries = $q_inqueries->archived();
        } else {
            $q_inqueries = $q_inqueries->notArchived();
        }

        // Dont take inquiries with status accepted if company not met
        $q_inqueries
            ->where(function ($q) use ($spheres, $onlyOffered) {
                $q->where(function ($q) use ($spheres, $onlyOffered) {
                    $q->whereHas('offers', function ($q) {
                        $q->where('company_id', Auth::user()->getCompany()->id);
                    });
                    if ( ! $onlyOffered) {
//                        $q->
//                        orWhere(function ($q) use ($spheres) {
//                            // Take all inquiries which accepts the sphere criterias
//                            $q->matchSpheres($spheres);
//                        });
                    }
                });
                if ( ! $onlyOffered) {
                    $q->orWhere(function ($q) use ($spheres) {
                        $q->doesntHave('offers');
//                            ->where(function ($q) use ($spheres) {
//                                // Take all inquiries which accepts the sphere criterias
//                                $q->matchSpheres($spheres);
//                            });
                    });
                }
            });

        // Where Spheres met



        $q_inqueries
            ->leftJoin('offers', function ($join) {
                $join
                    ->on('offers.inquiry_id', '=', 'inquiries.id')
                    ->where('offers.status', Offer::STATUS_ACCEPTED)
                    ->where('company_id', Auth::user()->getCompany()->id);
            })
            ->select('inquiries.*');
        if($orderBy){
            $q_inqueries->orderBy('offers.status', 'DESC')
            ->orderBy('inquiries.created_at', 'DESC');
        }

        return $q_inqueries;
    }

    public static function all()
    {
        return Inquiry::active();
    }

    public static function getAvailable($archived = false)
    {
        if ( ! Auth::check() || Auth::user()->isSystemUser()) {
            return self::all();
        }
        // If its a Regular user return its own inquries
        if (Auth::user()->isRegular())
        {
            return self::getRegular($archived);
        }

        return self::getBroker($archived);
    }

    public static function latest($number = 4)
    {
        $inquiries = Inquiry::active()->limit($number)->orderBy('id','desc')->get();

        return $inquiries;
    }
    public static function latestAll($number = 4)
    {
        $inquiries = Inquiry::limit($number)->orderBy('id','desc')->get();

        return $inquiries;
    }

    public static function getInquiryById($inquiryID)
    {
        if (Auth::check())
        {
            $inquiry = Inquiry::owns()->find($inquiryID);

            return $inquiry;
        }

        $inquiry = Inquiry::where('token', $inquiryID)->first();

        return $inquiry;
    }

    public static function create(array $data)
    {
        $locations = $data['location'];
        $property_types = $data['property_types'];
        $properties = $data['properties'];

        unset($data['property_types']);
        unset($data['properties']);
        unset($data['location']);

        $inquiry = Inquiry::create($data);

        $inquiry->cities()->sync($locations);
        $inquiry->property_type()->sync($property_types);
        $inquiry->properties()->sync($properties);

        try {
            $chainCities = json_encode($locations);
            $chainProperty_types = json_encode($property_types);
            $chainProperties = json_encode($properties);
            $chainServices = json_encode([$data['service_id']]);
            $isExist = \App\Chain::where([
//                'cities' => $chainCities,
                'propertytypes' => $chainProperty_types,
                'properties' => $chainProperties,
                'services' => $chainServices,
//                'user_id' => $data['user_id']
            ])->first();
            if(!$isExist) {
                $chain = \App\Chain::create([
                    'usertype' => 'customer',
                    'price_from' => $data['price_from'],
                    'price_to' => $data['price_to'],
                    'user_id' => $data['user_id'],
                    'cities' => $chainCities,
                    'propertytypes' => $chainProperty_types,
                    'properties' => $chainProperties,
                    'services' => $chainServices,
                ]);
            }
//            if($chain) {

//                \App\ChainService::create([
//                    'chain_id' => $chain->id, 'service_id' => $data['service_id']
//                ]);

//                $insertCity = [];
//                foreach($locations as $cityid){
//                    $insertCity[] = [ 'chain_id' => $chain->id, 'city_id' => $cityid ];
//                }
//                $insertProperty = [];
//                foreach($properties as $propid){
//                    $insertProperty[] = [ 'chain_id' => $chain->id, 'property_id' => $propid ];
//                }
//                $insertPropertyType = [];
//                foreach($property_types as $propid){
//                    $insertPropertyType[] = [ 'chain_id' => $chain->id, 'property_type_id' => $propid ];
//                }
                
//                \App\ChainCity::insert($insertCity);
//                \App\ChainProperty::insert($insertProperty);                
//                \App\ChainPropertyType::insert($insertPropertyType);
//            }
        }  catch (\Exception $e){
//            echo $e->getMessage();exit;
        }
        
        return $inquiry;
    }
}
