<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Omnipay\Omnipay;

class Paysera extends PaymentGateway
{
    /**
     * @return mixed
     */
    public function gateway()
    {
        $gateway = Omnipay::create('Paysera');

        $gateway->setProjectId(config('services.paysera.project_id'));
        $gateway->setPassword(config('services.paysera.password'));

        return $gateway;
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function notification()
    {
        $response = $this->gateway()
            ->acceptNotification()
            ->send();

        return $response;
    }

    /**
     * @param $order
     */
    public function getCancelUrl($order)
    {
        return route('checkout.cancelled.paysera', $order->id);
    }

    /**
     * @param $order
     */
    public function getReturnUrl($order)
    {
        return route('checkout.completed.paysera.view', $order->id);
    }

    /**
     * @param $order
     */
    public function getNotifyUrl($order)
    {
        return route('checkout.completed.paysera', $order->id);
    }

}
