<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class PaymentGateway extends Model
{
    abstract protected function gateway();
    abstract protected function getCancelUrl($order);
    abstract protected function getReturnUrl($order);

    /**
     * @param array $parameters
     * @return mixed
     */
    public function purchase(array $parameters)
    {
        $response = $this->gateway()
            ->purchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function complete(array $parameters)
    {
        $response = $this->gateway()
            ->acceptNotification()
            ->send();

        return $response;
    }

    /**
     * @param $amount
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }
}
