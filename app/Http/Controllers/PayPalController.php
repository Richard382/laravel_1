<?php

namespace App\Http\Controllers;

use App\Order;
use App\PayPal;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public static function checkout(Order $order)
    {
        $paypal = new PayPal;

        $response = $paypal->purchase([
            'amount' => $paypal->formatAmount($order->amount),
            'transactionId' => $order->token,
            'currency' => env('SHOP_CURRENCY'),
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
            'NOSHIPPING' => 1
        ]);

        if ($response->isRedirect()) {
            return $response->redirect();
        }

        return redirect()->route('payment.view', [
            'payment_type' => $order->model_type,
            'model_id' => $order->model_id
        ])->with([
            'message' => $response->getMessage(),
        ]);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function completed($order_id)
    {
        $order = Order::token($order_id)->firstOrFail();

        $paypal = new PayPal;

        $response = $paypal->complete([
            'amount' => $paypal->formatAmount($order->amount),
            'transactionId' => $order->token,
            'currency' => 'EUR',
            'cancelUrl' => $paypal->getCancelUrl($order),
            'returnUrl' => $paypal->getReturnUrl($order),
        ]);

        if ($response->isSuccessful())
        {
            $order->complete($response->getTransactionReference());

            return redirect()->route('payment.summary', ['token' => $order->token]);
        }

        return redirect()->route('payment.view', [
            'payment_type' => $order->model_type,
            'model_id' => $order->model_id
        ])->with([
            'message' => $response->getMessage(),
        ]);
    }

    public function cancelled($order_id)
    {
        $order = Order::token($order_id)->firstOrFail();

        return redirect()->route('payment.view', [
            'payment_type' => $order->model_type,
            'model_id' => $order->model_id
        ])->with([
            'message' => 'You have cancelled your recent PayPal payments!',
        ]);
    }
}
