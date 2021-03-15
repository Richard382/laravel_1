<?php

namespace App\Events;

use App\BrokerVisibility;
use App\Traits\User\Broker;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisibilityExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * @var BrokerVisibility
     */
    public $visibility;

    /**
     * OfferAccepted constructor.
     * @param $offer
     */
    public function __construct(BrokerVisibility $visibility)
    {
        $this->visibility = $visibility;
    }
}
