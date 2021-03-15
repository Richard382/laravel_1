<?php

namespace App\Http\Controllers;

use App\Order;
use App\Paysera;
use Illuminate\Http\Request;

class PayseraController extends Controller
{
    public static function checkout(Order $order)
    {
        $paysera = new Paysera;

        $response = $paysera->purchase([
            'amount' => $paysera->formatAmount($order->amount),
            'transactionId' => $order->token,
            'currency' => env('SHOP_CURRENCY'),
            'cancelUrl' => $paysera->getCancelUrl($order),
            'returnUrl' => $paysera->getReturnUrl($order),
            'notifyUrl' => $paysera->getNotifyUrl($order),
        ]);

        if ($response->isRedirect()) {
            $response->redirect();
        }

        return redirect()->route('payment.view', [
            'payment_type' => $order->model_type,
            'model_id' => $order->model_id
        ])->with([
            'message' => $response->getMessage(),
        ]);
    }

    public function done($order_id)
    {
        $order = Order::findOrFail($order_id);

        return redirect()->route('payment.summary', ['token' => $order->token]);
//        ->with([
//            'message' => 'Kai patvirtinsime jūsų apmokėjimą, gausite el. laišką su visa reikalinga informaciją!',
//        ]);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function completed($order_id)
    {
        $order = Order::findOrFail($order_id);

        $paypal = new Paysera;

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
        }
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function notify($order_id)
    {
        $order = Order::token($order_id)->firstOrFail();

        $paysera = new Paysera;

        $response = $paysera->notification();

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

    /**
     * @param $order_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelled($order_id)
    {
        $order = Order::token($order_id)->firstOrFail();

        return redirect()->route('payment.view', [
            'payment_type' => $order->model_type,
            'model_id' => $order->model_id
        ])->with([
            'message' => 'You have cancelled your recent Paysera payments!',
        ]);
    }
}
