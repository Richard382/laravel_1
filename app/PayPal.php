<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Omnipay\Omnipay;

class PayPal extends PaymentGateway
{
    /**
     * @return mixed
     */
    public function gateway()
    {
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername(config('services.paypal.username'));
        $gateway->setPassword(config('services.paypal.password'));
        $gateway->setSignature(config('services.paypal.signature'));
        $gateway->setTestMode(config('services.paypal.sandbox'));

        return $gateway;
    }

    /**
     * @param array $parameters
     */
    public function complete(array $parameters)
    {
        $response = $this->gateway()
            ->completePurchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param $order
     */
    public function getCancelUrl($order)
    {
        return route('checkout.cancelled.paypal', $order->token);
    }

    /**
     * @param $order
     */
    public function getReturnUrl($order)
    {
        return route('checkout.completed.paypal', $order->token);
    }
}
