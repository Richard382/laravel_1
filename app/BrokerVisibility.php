<?php

namespace App;

use App\Events\OrderCompleted;
use App\Events\OrderVisibilityCompleted;
use App\Helpers\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Request;

class BrokerVisibility extends Product
{
    public $timestamps = false;

    protected $appends = [
        'disabled'
    ];

    protected $fillable = [
        'company_id', 'taken_until'
    ];

    public function scopeExpired($query)
    {
        return $query
            ->whereNotNull('company_id')
            ->where('taken_until', '<', Carbon::now());
    }

    public function getDisabledAttribute()
    {
        return $this->isExpired() ? false : true;
    }

    public function isExpired()
    {
        if (! $this->company_id || ! $this->taken_until && $this->taken_until < Carbon::now()) {
            return true;
        }

        return false;
    }

    /**
     * Disable visibility
     */
    public function decline()
    {
        $this->update([
            'company_id' => null,
            'taken_until' => null
        ]);
    }

    /**
     * @param int $model_id
     * @param Order|null $order
     * @param null $when
     * @return Product
     */
    public static function find(int $model_id, Order $order = null, $when = null): Product
    {
        if (Request::has('months')) {
            $duration_id = Request::get('months');
        } else {
            $duration_id = $order->meta()->field('broker_visibility_duration');
        }

        $duration = BrokerVisibilityDurations::findOrFail($duration_id);

        $bv = self::findOrFail($model_id);

        if ($duration) {
            Request::session()->put('broker_visibility_duration', $duration);

            $bv->duration = $duration;
        }

        return $bv;
    }

    public function getProductName()
    {
        return sprintf('Matomumas - %s - %s', $this->name, $this->duration->name);
        // TODO: Implement getProductName() method.
    }

    public function getProductPrice($formatted = true)
    {
        $price = $this->price_per_month * $this->duration->months;

        if ($formatted) {
            return Shop::formatPrice($price);
        }

        return $price;
    }

    public function completed(Order $order)
    {
        $duration = $this->duration;
        unset($this->duration);

        $this->update([
            'company_id' => $order->owner->company->id,
            'taken_until' => $duration->countFromNow()
        ]);

        Cache::forget('topcompanies');

        $this->duration = $duration;

        event(new OrderVisibilityCompleted($order, $this));
    }

    public function afterProductCreated(Order $order)
    {
        $duration = Request::session()->get('broker_visibility_duration');

        $order->meta()->create([
            'order_meta_key' => 'broker_visibility_duration',
            'order_meta_value' => $duration->id
        ]);
    }
}
