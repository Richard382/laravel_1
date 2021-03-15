<?php

namespace App\Listeners;

use App\Events\ForceOffer;
use App\Mail\ChosenCompany;
use App\Mail\OfferAcceptedMail;
use App\Notifications\ForcedOffer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendForcedOffer
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
     * @param ForceOffer $event
     */
    public function handle(ForceOffer $event)
    {
        $receiver = $event->offer->company->representor;

        Mail::to($receiver->email)
            ->send(
                new ChosenCompany($event->inquiry)
            );

        $receiver->notify(new ForcedOffer($event->inquiry));
    }
}
