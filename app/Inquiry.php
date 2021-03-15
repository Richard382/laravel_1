<?php

namespace App;

use App\Traits\Helpers\Cacheable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Inquiry extends Model
{
    use Cacheable;

    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_READY_FOR_REVIEW = 'ready_for_review';
    const STATUS_REVIEWED = 'reviewed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'requirements',
        'price_from',
        'price_to',
        'in_hurry',
        'user_id',
        'token',
        'property_type_id',
        'service_id',
        'active',
        'contact_company',
        'archivedByOwner'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token',
        'offers',
        'user_id',
        'email',
        'name',
        'phone'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'sum_up',
        'offer_count',
        'made_an_offer',
        'offer_accepted',
        'end_time',
        'offer_declined',
        'payed_an_offer',
        'owner',
        'link',
        'pay_link',
        'service_type',
        'time_ago',
        'secured_data',
        'in_archive',
        'offer_price',
        'archived_by_owner'
//        'region_name',
//        'property_type_name'
    ];

    /**
     * Inquiry can belong to multipe cities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(City::class)
            ->select("cities.*",
                \DB::raw('if(cities.customer_display_name = "", cities.name, cities.customer_display_name) as name')
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class)
            ->select("services.*",
                \DB::raw('if(services.customer_display_name = "", services.type_user, services.customer_display_name) as name')
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function property_type()
    {
        return $this->belongsToMany(PropertyType::class)
            ->select("property_types.*",
                \DB::raw('if(property_types.customer_display_name = "", property_types.name, property_types.customer_display_name) as name')
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'inquiry_properties')
            ->select("properties.*",
                \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as name')
            );;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function archived()
    {
        return $this->belongsToMany(User::class, 'inquiries_archived');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotArchived(Builder $query)
    {
        return $query
            ->where(function(Builder $query) {
                $query->whereDoesntHave('archived', function ($query) {
                    $query->where('user_id', '=', Auth::user()->id);
                })
                ->orWhereDoesntHave('archived');
            });
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeArchived(Builder $query)
    {
        return $query->whereHas('archived', function($query) {
            $query->where('user_id', '=', Auth::user()->id);
            $query->where('erased', '=', false);
        });
    }

    /**
     * Scope a query to only include active inquiries.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include active inquiries.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOwns(Builder $query)
    {
        return $query->where('user_id', Auth::check() ? Auth::user()->id : 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotMine(Builder $query)
    {
        return $query->where('user_id', '!=', Auth::user()->id)->orWhereNull('user_id');
    }

    /**
     * Scope a query to only include active inquiries.
     *
     * @param $query
     * @return mixed
     */
    public function scopeFindByToken($query, $token)
    {
        return $query->where('token', $token);
    }

    public function scopeMatchSpheres(Builder $query, $spheres)
    {
        foreach ($spheres as $key => $sphere) {
            $regions = $sphere->regions->pluck('id')->toArray();
            $type = $sphere->type->id;
            $property = $sphere->property->id;
            $price_from = $sphere->price_from;
            $price_to = $sphere->price_to;

            if ($key === 0) {
                $query
                    ->where(function ($q) use ($regions, $type, $property, $price_from, $price_to) {
                        $q->whereHas('property_type', function (Builder $q) use ($type) {
                            $q->where('property_types.id', $type);
                        });

                        $q->whereHas('properties', function (Builder $q) use ($property) {
                            $q->where('properties.id', $property);
                        });

                        $q->whereHas('cities', function (Builder $q) use ($regions) {
                            $q->whereIn('cities.region_id', $regions);
                        });

                        if ($price_from || $price_to) {
                            $q->where(function ($q) use ($price_from, $price_to) {
                                if ($price_from) {
                                    $q->where('inquiries.price_from', '>', intval($price_from));
                                }

                                if ($price_to) {
                                    $q->where('inquiries.price_to', '<', intval($price_to));
                                }
                            });
                        }
                    });
            } else {
                $query
                    ->orWhere(function ($q) use ($regions, $type, $property, $price_from, $price_to) {
                        $q->whereHas('property_type', function (Builder $q) use ($type) {
                            $q->where('property_types.id', $type);
                        });

                        $q->whereHas('properties', function (Builder $q) use ($property) {
                            $q->where('properties.id', $property);
                        });

                        $q->whereHas('cities', function (Builder $q) use ($regions) {
                            $q->whereIn('cities.region_id', $regions);
                        });

                        if ($price_from || $price_to) {
                            $q->where(function ($q) use ($price_from, $price_to) {
                                if ($price_from) {
                                    $q->where('inquiries.price_from', '>', intval($price_from));
                                }

                                if ($price_to) {
                                    $q->where('inquiries.price_to', '<', intval($price_to));
                                }
                            });
                        }
                    });
            }
        }

        return $query;
    }

    public function getArchivedByOwnerAttribute()
    {
        return $this->creator ? $this->archived()->where('user_id', $this->creator->id)->exists() : 0;
    }

    public function getInArchiveAttribute()
    {
        if ( ! Auth::check()) {
            return null;
        }

        return $this->archived()->where('user_id', Auth::user()->id)->exists();
    }

    public function getSecuredDataAttribute()
    {
        if (! $this->payed_an_offer) {
            return NULL;
        }

        return [
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }

    public function getOwnerAttribute()
    {
        if (! Auth::check()) {
            return false;
        }

        if ($this->user_id ===
            Auth::user()->id) {
            return true;
        }

        return false;
    }

    public function getLinkAttribute()
    {
        return route('inquiry.show', ['inquiry' => $this->id]);
    }

    /**
     * @return bool
     */
    public function getOfferDeclinedAttribute()
    {
        $offer = $this->getCurrentUserOffer();

        if ( ! $offer) {
            return false;
        }

        if ($offer->status === Offer::STATUS_DECLINED) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getOfferCountAttribute()
    {
        return $this->offers->groupBy('company_id')->count();
    }

    /**
     * Get epiration time in miliseconds
     *
     * @role Global
     * @return bool|mixed
     */
    public function getEndTimeAttribute()
    {
        if (! Auth::check()) {
            return false;
        }

        $company = Auth::user()->getCompany();

        if (! $company) {
            return false;
        }

        $offer_accepted = $this->hasAcceptedOfferFrom($company);

        if ( ! $offer_accepted) {
            return false;
        }

        return max($offer_accepted->expire_at->getPreciseTimestamp(3) - Carbon::now()->getPreciseTimestamp(3), 0);
    }

    /**
     * Check if inquiry offer was accepted by current user company
     *
     * @role Broker
     * @return bool
     */
    public function getOfferAcceptedAttribute()
    {
        if (! Auth::check() || ! Auth::user()->isBroker()) {
            return false;
        }

        $offer_accepted = $this->hasAcceptedOfferFrom(Auth::user()->getCompany());

        if ($offer_accepted) {
            return $offer_accepted->id;
        }

        return false;
    }

    public function getPayLinkAttribute()
    {
        if ($this->offer_accepted) {
            return route('payment.view', ['payment_type' => 'offer', 'model_id' => $this->offer_accepted]);
        }

        return null;
    }

    public function getOfferPriceAttribute()
    {
        $offer = $this->getCurrentUserOffer();

        if ( ! $offer) {
            return null;
        }

        return $offer->getProductPrice(true);
    }

    /**
     * Check if current user made an offer
     *
     * @role Broker
     * @return bool
     */
    public function getMadeAnOfferAttribute()
    {
        if ( ! $this->getCurrentUserOffer()) {
            return false;
        }

        return true;
    }

    public function getPayedAnOfferAttribute()
    {
        $offer = $this->getCurrentUserOffer();

        if ( ! $offer) {
            return false;
        }

        if ($offer->payed) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSumUpAttribute()
    {
//        return Cache::rememberForever($this->cacheKey() . ':sum-up', function () {
            return sprintf('%1$s %6$s %2$s %6$s %3$s %6$s %4$s %6$s %5$s',
                $this->service?$this->service->type_user:'',
                implode(', ', $this->property_type->pluck('name')->toArray()),
                implode(', ', $this->properties->pluck('name')->toArray()),
                implode(', ', $this->cities->pluck('name')->toArray()),
                $this->getPriceRange(),
                $this->getPriceRange()?'-':''
            );
//        });
    }

    public function getServiceTypeAttribute()
    {
        return $this->service->type;
    }

    public function getTimeAgoAttribute()
    {
        $currentDateTime = new \DateTime();
        $passedDateTime = new \DateTime($this->created_at);
        $interval = $currentDateTime->diff($passedDateTime);
        //$elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
        $day = $interval->format('%a');
        $hour = $interval->format('%h');
        $min = $interval->format('%i');
        $seconds = $interval->format('%s');

        if($day > 7) {
            $dateArray = date_parse_from_format('Y/m/d', $this->created_at);
            $monthName = \DateTime::createFromFormat('!m', $dateArray['month'])->format('F');
            return $dateArray['day'] . " " . $monthName  . " " . $dateArray['year'];
        }else if($day >= 1 && $day <= 7 ){
            if($day == 1) return sprintf('Prieš %s d.', $day);
            return sprintf('Prieš %s d.', $day);
        }else if($hour >= 1 && $hour <= 24){
            if($hour == 1) return sprintf('Prieš %s val.', $hour);
            return sprintf('Prieš %s val.', $hour);
        }else if($min >= 1 && $min <= 60){
            if($min == 1) return sprintf('Prieš %s min.', $min);
            return sprintf('Prieš %s min.', $min);
        }else if($seconds >= 1 && $seconds <= 60){
            if($seconds == 1) return sprintf('Prieš %s sek.', $seconds);
            return sprintf('Prieš %s sek.', $seconds);
        }
    }

//    public function getPropertyTypeNameAttribute()
//    {
//        return implode(', ', $this->property_type->pluck('name')->toArray());
//    }
//
//    public function getRegionNameAttribute()
//    {
//        return implode(', ', $this->cities->pluck('name')->toArray());
//    }

    public function hasOfferFrom(Company $company)
    {
        return $this->offers()->madeBy($company->id)->first();
    }

    public function hasAcceptedOfferFrom(Company $company)
    {
        return $this->offers()->madeBy($company->id)->accepted()->first();
    }

    /**
     * Get Accepted offer
     *
     * @return mixed
     */
    public function getAcceptedOffer()
    {
        return $this->offers()->accepted()->first();
    }

    public function getPriceRange()
    {
        if((double)$this->price_from) {
            return sprintf(
                'nuo %s eur. iki %s eur.',
                $this->price_from ? $this->price_from : 'x',
                $this->price_to ? $this->price_to : 'x'
            );
        }else{
            return "";
        }
    }

    /**
     * @return Offer|bool
     */
    public function getCurrentUserOffer()
    {
        if (! Auth::check() || ! Auth::user()->isBroker()) {
            return false;
        }

        $offer = $this->offers->where('company_id', Auth::user()->getCompany()->id)->first();

        if ( ! $offer) {
            return false;
        }

        return $offer;
    }

    /**
     * Get Real Creator
     *
     * @return User|mixed
     */
    public function getRealCreatorUser()
    {
        return $this->user_id ? $this->creator : new User([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);
    }

    /**
     * @visible both
     * @return mixed
     */
    public function getCreatorName()
    {
        return $this->getRealCreatorUser()->name;
    }

    /**
     * Get inquiry ID - identifier
     *
     * @visible both
     * @return mixed
     */
    public function getID()
    {
        return $this->user_id ? $this->id : $this->token;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->token ? route('inquiry.show.public', ['token' => $this->token]) : route('inquiry.show', ['inquiry' => $this->id]);
    }

    /**
     * @return bool
     */
    public function archivable()
    {
        if ((Auth::user()->isBroker() && $this->payed_an_offer) || (Auth::user()->isRegular() && ! $this->active)) {
            return true;
        }

        return false;
    }

    public function eraseFromArchive()
    {
        $this
            ->archived()
            ->where('user_id', Auth::user()->id)
            ->update(['erased' => 1]);

        return $this;
    }

    /**
     * Archive inquiry
     *
     * @return $this
     */
    public function archive()
    {
        $this->archived()->attach(Auth::user()->id);

        return $this;
    }

    /**
     * Deactivate inquiry, make invisible for companies
     *
     * @return $this
     */
    public function activate()
    {
        $this->active = 1;

        $this->save();

        return $this;
    }

    /**
     * Deactivate inquiry, make invisible for companies
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->active = 0;

        $this->save();

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->save();
    }
}
