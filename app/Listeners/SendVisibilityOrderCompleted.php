<?php

namespace App\Listeners;

use App\Events\OrderVisibilityCompleted;
use App\Mail\NewInvoice;
use App\Mail\OrderCompletedMail;
use App\Mail\OrderCompletedRegularMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendVisibilityOrderCompleted
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
     * @param  object  $event
     * @return void
     */
    public function handle(OrderVisibilityCompleted $event)
    {
        $receiver = $event->order->owner;

        Mail::to($receiver->email)
            ->send(
                new \App\Mail\OrderVisibilityCompleted($event->order, $event->brokerVisibility)
            );

        if (env('INVOICER_EMAIL')) {
            Mail::to(env('INVOICER_EMAIL'))
                ->send(
                    new NewInvoice($event->order)
                );
        }

        $receiver->notify(new \App\Notifications\OrderVisibilityCompleted($event->order, $event->brokerVisibility));
    }
}
