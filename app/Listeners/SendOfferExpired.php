<?php

namespace App\Listeners;

use App\Events\OfferExpired;
use App\Notifications\OfferExpiredCompany;
use App\Notifications\OfferExpiredRegular;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOfferExpired implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OfferExpired  $event
     * @return void
     */
    public function handle(OfferExpired $event)
    {
        $inquiry_receiver = $event->offer->inquiry->getRealCreatorUser();
        $company_receiver = $event->offer->company->representor;

        $company_receiver->notify(new OfferExpiredCompany($event->offer));
        $inquiry_receiver->notify(new OfferExpiredRegular($event->offer));
    }
}
