<?php

namespace App\Listeners;

use App\Events\OfferAccepted;
use App\Mail\OfferAcceptedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOfferAccepted implements ShouldQueue
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
     * @param  OfferAccepted  $event
     * @return void
     */
    public function handle(OfferAccepted $event)
    {
        $receiver = $event->offer->company->representor;

        Mail::to($receiver->email)
            ->send(
                new OfferAcceptedMail($event->offer, $event->inquiry)
            );

        $receiver->notify(new \App\Notifications\OfferAccepted($event->inquiry));
    }
}
