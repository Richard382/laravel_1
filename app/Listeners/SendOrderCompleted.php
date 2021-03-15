<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\NewInvoice;
use App\Mail\OrderCompletedMail;
use App\Mail\OrderCompletedRegularMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderCompleted implements ShouldQueue
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
     * @param  OrderCompleted  $event
     * @return void
     */
    public function handle(OrderCompleted $event)
    {
        $receiver = $event->order->owner;
        $otherReceiver = $event->inquiry->getRealCreatorUser();

        Mail::to($receiver->email)
            ->send(
                new OrderCompletedMail($event->order, $event->inquiry)
            );

        if (env('INVOICER_EMAIL')) {
            Mail::to(env('INVOICER_EMAIL'))
                ->send(
                    new NewInvoice($event->order)
                );
        }

//        Mail::to($otherReceiver->email)
//            ->send(
//                new OrderCompletedRegularMail($event->order, $event->inquiry)
//            );

        $receiver->notify(new \App\Notifications\OrderCompleted($event->inquiry));
    }
}
