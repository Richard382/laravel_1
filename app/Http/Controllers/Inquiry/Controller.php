<?php

namespace App\Http\Controllers\Inquiry;

use App\Inquiry;
use App\PropertyType;
use App\Repositories\InquiryRepository;
use App\Repositories\RegionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\InquiryComplainMail;

class Controller extends \App\Http\Controllers\Controller
{
    public function viewPublic(Request $request, $token)
    {
        $inquiry = Inquiry::FindByToken($token)->first();

        if ( ! $inquiry)
        {
            abort(404);
        }

        return $this->view($request, $inquiry);
    }

    public function public()
    {
        $inquiries = InquiryRepository::all();

        $regions = RegionRepository::all();

        $property_types = Cache::rememberForever('property_types', function () {
            return PropertyType::with('properties')->get();
        });

        return view('inquiries.public', [
            'inquiries' => $inquiries->paginate(),
            'regions' => $regions,
            'property_types' => $property_types
        ]);
    }

    public function view(Request $request, Inquiry $inquiry)
    {
        // We return 404 if inquiry is made by registrated person and current person is not that person
        if ( Auth::check() && $inquiry->user_id !== Auth::user()->id )
        {
            return abort(404);
        }

        $offers = $inquiry->offers()->orderBy('status', 'asc')->with('company')->paginate();

        if ($request->ajax()) {
            return response()->json([
                'items' => $offers->items(),
                'total' => $offers->total()
            ], 201);
        }
        
        return view('inquiries.view', [
            'inquiry' => $inquiry,
            'accepted' => $inquiry->active ? 0 : 1,
            'offers' => $offers
        ]);
    }

    public function index(Request $request)
    {
        $expand = $request->expand;
        $inquiries = InquiryRepository::getBroker(false, $request->has('klientu-uzklausos'),false);
        $testInq = clone $inquiries;
        $testInq = $testInq->get();
        $testInqIds = [];
        $sphereData = $this->checkProvideSphereData(\Auth::user());
        $testInqIds = $this->checkProvideHasMatchChain($testInq,$sphereData);
        
        if ($request->ajax()) {

            if ($request->has('search') && $request->get('search')) {
                $search = $request->get('search');

                $inquiries = $inquiries
                    ->where(function($q) use($search) {
                        $q->whereHas('service', function ($q) use ($search) {
                            $q->where('type_user', 'LIKE', '%' . $search . '%' );
                        })
                        ->orWhereHas('property_type', function($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%' );
                        })
                        ->orWhereHas('properties', function($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%' );
                        })
                        ->orWhereHas('cities', function($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%' );
                        });
                    });
            }

            if ($request->has('service_type') && $request->get('service_type')) {
                $service_type = $request->get('service_type');

                $inquiries = $inquiries
                    ->where(function($q) use($service_type) {
                        $q->whereHas('service', function ($q) use ($service_type) {
                            $q->where('type', $service_type);
                        });
                    });
            }

            if ($request->has('region') && $request->get('region')) {
                $region = $request->get('region');

                $inquiries = $inquiries
                    ->where(function($q) use($region) {
                        $q->whereHas('cities', function ($q) use ($region) {
                            $q->where('region_id', $region);
                        });
                    });
            }

            if ($request->has('type') && $request->get('type')) {
                $type = $request->get('type');

                $inquiries = $inquiries
                    ->where(function($q) use($type) {
                        $q->whereHas('property_type', function ($q) use ($type) {
                            $q->where('property_types.id', $type);
                        });
                    });
            }

        }
            if($expand){
                if (($key = array_search($expand, $testInqIds)) !== false) {
                    unset($testInqIds[$key]);
                }
                $testInqIds = array_merge([$expand],$testInqIds);
            }
            
            $inquiries = $inquiries->whereIn('inquiries.id',$testInqIds);
            
            if($expand){
                $ids = implode(',', $testInqIds);
                
                $inquiries = $inquiries->orderBy('offers.status', 'DESC')
                        ->orderByRaw("IF(inquiries.id = $expand, 0,1)")
                    ->orderBy('inquiries.created_at', 'DESC');
            }else{
                $inquiries = $inquiries->orderBy('offers.status', 'DESC')
                ->orderBy('inquiries.created_at', 'DESC');
            }
            
            $inquiries = $inquiries->paginate();

        $itemsTransformed = $inquiries->getCollection()->map(function($item) {
                $narr = $item->toArray();
                $propid = null;
                
                
                $hasPrice = \App\Price::where('service',$item->service_id)->first();
                if($hasPrice){
                    $narr['offer_price'] = \App\Helpers\Shop::formatPrice($hasPrice->price);
                }
                if(isset($item->properties) && $item->properties && count($item->properties)){
                    $propid = $item->properties[0]['id'];
                    $getProp = \App\ChainProperty::select('chain_service.provider_fee')
                            ->where('property_id',$propid)
                            ->join('chain_service','chain_service.chain_property_id','chain_properties.id')
                            ->where('chain_service.service_id',$item->service_id)
                            ->first();

                            if($getProp && (float)$getProp->provider_fee > 0){
                        $narr['offer_price'] = \App\Helpers\Shop::formatPrice($getProp->provider_fee);
                    }
                }
                
                return $narr;
        })->toArray();
        
        $inquiriesss = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsTransformed,
            $inquiries->total(),
            $inquiries->perPage(),
            $inquiries->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $inquiries->currentPage()
                ]
            ]
        );
        
        if ($request->ajax()) {
            $inquiries = [];
            $temp_inquiries = $inquiriesss->items();
            foreach($temp_inquiries as $inquiry) {
                // dd($inquiry->property_type[0]->name);
                $region = substr_count($inquiry['sum_up'], ',');
                if($region > 58) {
                    $t_inquiry = $inquiry;
                    $t_inquiry['sum_up'] = "Visa Lietuva";
                    $inquiries[] = $t_inquiry;
                }else {
                    $inquiries[] = $inquiry;
                }
            }
            
            return response()->json([
                'items' => $inquiries
            ], 201);
        }
        
        $regions = RegionRepository::all();

        $property_types = PropertyType::with('properties')->get();
        
        return view('inquiries.index', [
            'inquiries' => $inquiriesss,
            'regions' => $regions,
            'property_types' => $property_types,
            'expand'=>$expand
        ]);
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
    public function checkProvideHasMatchChain($inquiries, $spheres) {
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
//                        && ($inqprice_from < 1 || $inqprice_from >= $spherepricefrom)
//                        && ($inqprice_from < 1 || $inqprice_from < $spherepriceto)
//                        && ($inqprice_to < 1 || $inqprice_to <= $spherepriceto)
                        && ($inqprice_from < 1 || $inqprice_from <= $spherepriceto)
                        && ($inqprice_to < 1 || $inqprice_to >= $spherepricefrom)
                    ) {
                        $inqIds[] = $inquiry->id;
                        break;
                    }
                }
//                return false;
            }
            return $inqIds;
        }catch(\Exception $e){
            return false;
        }
    }
    
    public function my(Request $request)
    {
        $inquiries = InquiryRepository::getRegular()->orderBy('id','desc')->paginate();

        if ($request->ajax()) {
            return response()->json([
                'items' => $inquiries->items()
            ], 200);
        }

        return view('inquiries.my', [
            'inquiries' => $inquiries,
        ]);
    }
    
    public function deleteInquiry(Request $request, $inquiry) {
        try
        {   $inquiry = \App\Inquiry::where('id',$inquiry)->first();
            if($inquiry)
            {
                
                $isDel = $inquiry->delete();
                if($isDel){
                    return response()->json(['success'=>true]);
                }
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'error'=>$e->getMessage()]);
        }
        return response()->json(['success'=>false]);
    }
    
    public function archiveComplain(){
        try {
            $mail = Mail::to('info@cont.lt')
                ->send(
                    new InquiryComplainMail()
            );
            if (count(Mail::failures()) > 0) {
                print_r(Mail::failures());exit;
            }
        } catch (Exception $e) {

            if (count(Mail::failures()) > 0) {
                print_r(Mail::failures());exit;
            }
            echo $e->getMessage();exit;
        }
        return response()->json(['success'=>true, "message"=>"Mail sent successfully.","mail"=>$mail]);
    }
}
