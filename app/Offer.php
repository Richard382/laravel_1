<?php

namespace App;

use App\Events\OrderCompleted;
use App\Helpers\Shop;
use App\Repositories\RatingRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Offer extends Product
{
    const BASE_PRICE = 40;
    const STATUS_DEFAULT = 2;
    const STATUS_ACCEPTED = 1;
    const STATUS_DECLINED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'text',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expire_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'accepted',
        'rated',
        'endtime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Accepted offers
     *
     * @param $query
     * @return mixed
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeMadeBy($query, $id)
    {
        return $query->where('company_id', $id);
    }

    public function getRatedAttribute()
    {
        return RatingRepository::exists($this->company, $this->inquiry);
    }

    /**
     * Get not expired offers
     *
     * @param $query
     * @return mixed
     */
    public function scopeNotExpiredAccepts($query)
    {
        return $query
            ->accepted()
            ->where('payed', 0)
            ->where('expire_at', '>', Carbon::now());
    }

    /**
     * Expired accepted offers
     *
     * @param $query
     * @return mixed
     */
    public function scopeExpiredAccepts($query)
    {
        return $query
            ->accepted()
            ->where('payed', 0)
            ->where('expire_at', '<', Carbon::now());
    }

    /**
     * @return bool
     */
    public function getAcceptedAttribute()
    {
        if ($this->status === self::STATUS_ACCEPTED) {
            return true;
        }

        return false;
    }

    /**
     * To accept offer
     *
     * @role Regular
     * @return $this
     */
    public function accept()
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->expire_at = Carbon::now()->addMinutes(60);

        $this->save();

        return $this;
    }

    /**
     * To decline offer
     *
     * @return $this
     */
    public function decline()
    {
        $this->status = self::STATUS_DECLINED;
        $this->expire_at = null;

        $this->save();

        return $this;
    }

    /**
     * @param int $model_id
     * @param Order|null $order
     * @param null $when
     * @return Product
     */
    public static function find(int $model_id, Order $order = null, $when = null): Product
    {
        if (! Auth::check() && ! $when) {
            return parent::find($model_id);
        }

        switch ($when) {
            case 'order-regular-details' :

                return Offer::where('id', $model_id)
                    ->firstOrFail();

                break;

            case 'order-summary':

                return Offer::where('id', $model_id)
                    ->where('company_id', Auth::user()->company->id)
                    ->firstOrFail();

                break;

            case 'complete-order':

                return Offer::NotExpiredAccepts()
                    ->where('id', $model_id)
                    ->firstOrFail();

                break;
        }

        return Offer::NotExpiredAccepts()
            ->where('id', $model_id)
            ->where('company_id', Auth::user()->company->id)
            ->firstOrFail();

    }

    public function getProductName()
    {
        return sprintf('Užklausa Nr.: %s', $this->inquiry->id);
    }

    public function getProductPrice($formatted = true)
    {
        if ($formatted) {
            return Shop::formatPrice(self::BASE_PRICE);
        }

        return self::BASE_PRICE;
    }
    public function getProductServicePrice($formatted = true)
    {
        $price = self::BASE_PRICE;
        $priceInq1 = \App\Inquiry::select('prices.price')->where('inquiries.id',$this->inquiry_id)
                ->join('prices','prices.service','inquiries.service_id')
                ->first();
        $priceInq = \App\Inquiry::select('*')
                ->where('inquiries.id',$this->inquiry_id)
                ->with('properties')
                ->first();
        
        if($priceInq && count($priceInq->properties)){
            $propid = $priceInq->properties[0]['id'];
            $getProp = \App\ChainProperty::select('chain_service.provider_fee')
                    ->where('property_id',$propid)
                    ->join('chain_service','chain_service.chain_property_id','chain_properties.id')
                    ->where('chain_service.service_id',$priceInq->service_id)
                    ->first();
            if($getProp) {
                $price = $getProp->provider_fee;
            }
        } 
        if($priceInq1 && !$price){
            $price = $priceInq1->price;
        }
        if ($formatted) {
            return Shop::formatPrice($price);
        }

        return $price;
    }

    public function getCompletedText(Order $order): String
    {
        return sprintf('<p>užklausao informacija sėkmingai <u>apmokėta</u> ir išsiųsta el. paštu į <strong>%s</strong></p>', $order->owner->email);
    }

    public function completed(Order $order)
    {
        $this->payed = 1;
        $this->expire_at = null;
        $this->status = Offer::STATUS_DEFAULT;

        $this->inquiry->deactivate();

        $this->inquiry->setStatus(Inquiry::STATUS_READY_FOR_REVIEW);

        $this->save();

        event(new OrderCompleted($order, $this->inquiry));
    }
    
    public function getEndtimeAttribute() {
        try{
            if($this->expire_at){
                return max($this->expire_at->getPreciseTimestamp(3) - Carbon::now()->getPreciseTimestamp(3), 0);
            }
        }catch(\Exception $e){}
        return 0;
    }
}
