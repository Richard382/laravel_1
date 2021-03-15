<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

abstract class Product extends Model
{
    const PRODUCT_TYPE_OFFER = 'offer';
    const PRODUCT_TYPE_VISIBILITY = 'visibility';

    /**
     * @param string $type
     * @param int $model_id
     * @param Order|null $order
     * @param String|bool $when
     * @return static
     */
    public static function getModel(string $type, int $model_id, Order $order = null, $when = false): self
    {
        switch ($type) {
            case self::PRODUCT_TYPE_OFFER :

                $model = Offer::class;

                break;

            case self::PRODUCT_TYPE_VISIBILITY :

                $model = BrokerVisibility::class;

                break;
        }

        return $model::find($model_id, $order, $when);
    }

    public function getCompletedText(Order $order): String
    {
        return '<p>Paslauga sėkmingai <u>apmokėta</u>.';
    }

    public function afterProductCreated(Order $order)
    {

    }

    abstract static function find(int $model_id, Order $order = null, $when = null): self;

    abstract function getProductName();

    abstract function getProductPrice(bool $formatted = true);

    abstract function completed(Order $order);
}
