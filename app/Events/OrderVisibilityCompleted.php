<?php

namespace App\Events;

use App\BrokerVisibility;
use App\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderVisibilityCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * @var Order
     */
    public $order;

    public $brokerVisibility;

    /**
     * OrderCompleted constructor.
     * @param Order $order
     * @param BrokerVisibility $brokerVisibility
     */
    public function __construct(Order $order, BrokerVisibility $brokerVisibility)
    {
        $this->order = $order;
        $this->brokerVisibility = $brokerVisibility;
    }
}
