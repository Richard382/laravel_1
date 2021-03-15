<?php

namespace App\Listeners;

use App\Events\NewOffer;
use App\Mail\NewOfferMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewOffer implements ShouldQueue
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
     * @param NewOffer $event
     */
    public function handle(NewOffer $event)
    {
        $receiver = $event->inquiry->getRealCreatorUser();

        Mail::to($receiver->email)
            ->send(
                new NewOfferMail($event->offer, $event->inquiry)
            );

        if ($event->inquiry->user_id) {
            $receiver->notify(new \App\Notifications\NewOffer($event->inquiry));
        }
    }
}
