<?php

namespace App;

use App\Traits\Helpers\Cacheable;
use App\Traits\Helpers\Avatar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use Avatar,  Cacheable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'website', 'represent_video', 'avatar',
    ];

    protected $guarded = [
        'language', 'paths'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'avatar_url',
        'rating',
        'link',
        'spheres_sum_up'
    ];

    /**
     * Spheres relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spheres()
    {
        return $this->hasMany(Sphere::class);
    }

    /**
     * Rates relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function visibility()
    {
        return $this->hasOne(BrokerVisibility::class);
    }

    /**
     * Languages relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function representor()
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certificates()
    {
        return $this->hasMany(Certificates::class);
    }

    /**
     * Return Rating to be precise of 0.5
     *
     * @cached 24hrs
     * @return float|int
     */
    public function getRatingAttribute()
    {
        return Cache::remember($this->cacheKey() . ':rating', 1440, function () {
            return floor($this->rates()->avg('rating') * 2) / 2;
        });
    }

    /**
     * Return Rating relationship
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        return route('companies.show', ['company' => $this->id]);
    }

    public function isOwner()
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->id === Auth::user()->company_id;
    }

    /**
     * Get company languages
     *
     * @cached forever
     * @return mixed
     */
    public function getLanguages()
    {
        return Cache::rememberForever($this->cacheKey() . ':languages', function () {
            return $this->languages()->get();
        });
    }

    /**
     * Get Company spheres
     *
     * @cached forever
     * @return mixed
     */
    public function getSpheres()
    {
        return Cache::rememberForever($this->id . ':spheres', function () {
            return $this->spheres()->with(['type', 'regions'])->get();
        });
    }

    /**
     * Get Company comments
     *
     * @cached 24hrs
     * @return mixed
     */
    public function getComments()
    {
        return Cache::remember($this->cacheKey() . ':comments', 1440, function () {
            return $this->rates()->with('inquiry.service')->get();
        });
    }

    public function appendProfileData()
    {
        $this->languages_list = $this->languages->pluck('id')->toArray();
        $paths = [];
        foreach ($this->spheres as $sphere) {
            
            $fff = ChainProperty::
                select("*",
                    \DB::raw('if(services.provider_display_name = "", services.type_user, services.provider_display_name) as name')
                )
                ->where('property_id',$sphere->property_id)
                ->join('chain_service','chain_service.chain_property_id','chain_properties.id')
                ->join('services','services.id','chain_service.service_id')
                ->get();
            $props = ChainPropertyType::
                select("*",
                        \DB::raw('if(properties.provider_display_name = "", properties.name, properties.provider_display_name) as name')
                )
                ->where('chain_property_type.property_type_id',$sphere->property_type_id)
                ->join('chain_properties','chain_properties.chain_property_type_id','chain_property_type.id')
                ->join('properties','properties.id','chain_properties.property_id')
                ->get();
//            $fff = ChainService::
//                    select("*",
//                            \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as name')
//                    )
//                    
//                    ->where('property_id',$sphere->property_id)
//                    ->join('services','services.id','chain_service.service_id')
//                    ->get();
//            $fff = Sphere::where('id',$sphere->id)
//                    ->with('services')->first();
//            $services = [];
//            foreach($fff->services as $sss){
//                $sss->name = $sss->type_broker;
//            }
            $sphereRegions = \DB::table('region_sphere')
                    ->select('city_id as id')
                    ->where('sphere_id',$sphere->id)->pluck('id')->toArray();
            
            $paths[] = [
                'data' => [
                    'property_type' => $sphere->type?$sphere->type->id:0,
                    'properties' => $sphere->property?$sphere->property->id:0,
                    'regions' => $sphereRegions,
                    'price_from' => $sphere->price_from,
                    'price_to' => $sphere->price_to,
                    'services' => $sphere->services->pluck('id')->toArray()
                ],
                'all_services' => false,
                'done' => true,
                'prev_step' => 5,
                'step' => 5,
//                'temporary_properties' => $sphere->type->properties,
                'temporary_properties' => $props,
                'services' => $fff
            ];
        }

        $this->paths = $paths;
        $this->paths_form = collect($paths)->pluck('data')->toArray();
        $this->certificates_list = $this->certificates->pluck('url_full')->toArray();

        return $this;
    }

    /**
     * Return sphere type sum-up
     *
     * @cached forever
     * @return mixed
     */
    public function getSpheresSumUpAttribute()
    {
        return Cache::rememberForever($this->cacheKey() . ':typessumup', function ()
        {
            $spheres = $this->getSpheres();

            $propert_types = [];

            foreach ($spheres as $sphere)
            {
                if (! isset($sphere->type->name)) {
                    continue;
                }

                $propert_types[] = $sphere->type->name;
            }

            return implode(', ', $propert_types);
        });


    }

    /**
     * Add penalty to company
     *
     * @return $this
     */
    public function addPenalty(Inquiry $inquiry)
    {
        $rating = Rating::create([
            'company_id' => $this->id,
            'rating' => 1,
            'inquiry_id' => $inquiry->id,
            'text' => 'Automatiškai sugeneruotas įvertinimas dėl netsakyto užklausao!'
        ]);

        $this->rates->add($rating);
        $this->save();

        return $this;
    }
}
