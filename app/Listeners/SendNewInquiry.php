<?php

namespace App\Listeners;

use App\Events\NewInquiry;
use App\Mail\NewInquiryMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNewInquiry implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewInquiry  $event
     * @return void
     */
    public function handle(NewInquiry $event)
    {
        Mail::to($event->inquiry->getRealCreatorUser()->email)
            ->send(
                new NewInquiryMail($event->inquiry)
        );
        
        //added on 14 oct 2020
        $users = \App\User::where('role_id','3')->get();
        $providerMail = [] ;
        foreach($users as $receiver){
            $check = $this->checkProvideHasMatchChain($event->inquiry,$receiver);
            if($check){
                $providerMail[] = $receiver->email;
                $receiver->notify(new \App\Notifications\NewInquiry($event->inquiry));
            }
        }
        if(count($providerMail)){
            Mail::to($providerMail)
                ->send(
                    new \App\Mail\NewInquiryMailProvider($event->inquiry)
            );
        }
    }
    
    public function checkProvideHasMatchChain($inquiry, $user) {
        try{
            $inquiry = \App\Inquiry::where('id',$inquiry['id'])
                ->with('property_type')
                ->with('properties')
                ->with('service')
                ->with('cities')
                ->first();
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
                $cities[] = $data['id'];
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
                $regionIds = \DB::table('region_sphere')->where('sphere_id',$sphere->id)->pluck('city_id')->toArray();
 
                if(in_array($sphere->property_type_id, $propertyTypes)
                    && in_array($sphere->property_id, $properties)
                    && in_array($service, $serviceIds)
                    && count(array_intersect($cities, $regionIds))
                    && ($inqprice_from < 1 || $inqprice_from <= $spherepriceto)
                    && ($inqprice_to < 1 || $inqprice_to >= $spherepricefrom)
                ){
                    return true;
                }
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkMatchedProvider($inquiry, $user) {
        try{
           //inquire from the vuejs
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
                $cities[] = $data['id'];
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
                $regionIds = \DB::table('region_sphere')->where('sphere_id',$sphere->id)->pluck('city_id')->toArray();
 
                if(in_array($sphere->property_type_id, $propertyTypes)
                    && in_array($sphere->property_id, $properties)
                    && in_array($service, $serviceIds)
                    && count(array_intersect($cities, $regionIds))
                    && ($inqprice_from < 1 || $inqprice_from <= $spherepriceto)
                    && ($inqprice_to < 1 || $inqprice_to >= $spherepricefrom)
                ){
                    return true;
                }
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
