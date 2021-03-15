<?php

namespace App\Events;

use App\Inquiry;
use App\Offer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForceOffer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * ForceOffer constructor.
     * @param Offer $offer
     */
    public function __construct(Offer $offer, Inquiry $inquiry)
    {
        $this->offer = $offer;
        $this->inquiry = $inquiry;
    }
}
