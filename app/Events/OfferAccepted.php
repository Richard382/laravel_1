<?php

namespace App\Events;

use App\Inquiry;
use App\Offer;
use Illuminate\Queue\SerializesModels;

class OfferAccepted
{
    use SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * OfferAccepted constructor.
     * @param $offer
     */
    public function __construct(Offer $offer, Inquiry $inquiry)
    {
        $this->offer = $offer;
        $this->inquiry = $inquiry;
    }
}
