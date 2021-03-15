<?php

namespace App\Mail;

use App\Inquiry;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;

    public $inquiry;

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
            ->subject('CONT: Nr. ' . $this->inquiry->id .  ' Jūsų pasiūlymas priimtas!')
            ->markdown('emails.offer.force');
    }
}
