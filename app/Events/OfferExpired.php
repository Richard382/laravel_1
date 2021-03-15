<?php

namespace App\Events;

use App\Offer;
use Illuminate\Queue\SerializesModels;

class OfferExpired
{
    use SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * OfferAccepted constructor.
     * @param $offer
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
    }
}
