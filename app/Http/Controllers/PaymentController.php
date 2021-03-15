<?php

namespace App\Http\Controllers;

use App\Helpers\Token;
use App\Mail\OrderCompletedMail;
use App\Offer;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Validate payment process
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return Validator::make($data, [
           'gateway' => 'required|in:paypal,paysera'
        ]);
    }

    public function view(Request $request, $payment_type, $model_id)
    {
        $model = Product::getModel($payment_type, $model_id);

        return view('payments.view', compact('model'));
    }

    public function process(Request $request, $payment_type, $model_id)
    {
        
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        $product = Product::getModel($payment_type, $model_id);

        $order = Order::create([
            'amount' => $product->getProductServicePrice(false),
            'user_id' => Auth::user()->id,
            'token' => Str::random(40),
            'model_type' => $payment_type,
            'model_id' => $model_id,
            'payment_status' => Order::PAYMENT_PENDING,
            'payment_method' => strtolower($request->get('gateway')),
        ]);

        $order->products()->create([
            'name' => $product->getProductName(),
            'amount' => $product->getProductServicePrice(false),
        ]);

        $product->afterProductCreated($order);

        switch ($request->get('gateway'))
        {
            case 'paypal':

                return PayPalController::checkout($order);

                break;

            case 'paysera':

                return PayseraController::checkout($order);

                break;
        }
    }

    public function summary($token)
    {
        $order = Order::token($token)->firstOrFail();

        return view('payments.summary', compact('order'));
    }
}
