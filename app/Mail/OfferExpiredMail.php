<?php

namespace App\Mail;

use App\Inquiry;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    public $inquiry;

    /**
     * OfferExpiredRegularMail constructor.
     * @param Offer $offer
     * @param Inquiry $inquiry
     */
    public function __construct(Offer $offer, Inquiry $inquiry)
    {
        $this->offer = $offer;
        $this->inquiry = $inquiry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('CONT: Nr. ' . $this->inquiry->id . ' - Baigėsi apmokėjimo terminas!')
            ->markdown('emails.offer.expired');
    }
}
