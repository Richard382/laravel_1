<?php

namespace App\Events;

use App\Inquiry;
use App\Offer;
use Illuminate\Queue\SerializesModels;

class NewOffer
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
     * NewOffer constructor.
     *
     * @param Offer $offer
     * @param Inquiry $inquiry
     */
    public function __construct(Offer $offer, Inquiry $inquiry)
    {
        $this->offer = $offer;
        $this->inquiry = $inquiry;
    }
}
