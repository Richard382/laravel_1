<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Order;

class DownloadController extends Controller
{

    public function index(Order $order)
    {
        return $order->generateInvoice()->download($order->getInvoiceName());
    }
}
