<?php

namespace App\Events;

use App\Inquiry;
use App\Offer;
use App\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * OrderCompleted constructor.
     * @param Order $order
     * @param Inquiry $inquiry
     */
    public function __construct(Order $order, Inquiry $inquiry)
    {
        $this->order = $order;
        $this->inquiry = $inquiry;
    }
}
