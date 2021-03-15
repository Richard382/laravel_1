<?php

namespace App\Providers;

use App\Events\ForceOffer;
use App\Events\NewInquiry;
use App\Events\NewOffer;
use App\Events\OfferAccepted;
use App\Events\OfferExpired;
use App\Events\OrderCompleted;
use App\Events\OrderVisibilityCompleted;
use App\Listeners\SendForcedOffer;
use App\Listeners\SendNewInquiry;
use App\Listeners\SendNewOffer;
use App\Listeners\SendOfferAccepted;
use App\Listeners\SendOfferExpired;
use App\Listeners\SendOrderCompleted;
use App\Listeners\SendVisibilityOrderCompleted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewOffer::class => [
            SendNewOffer::class
        ],
        NewInquiry::class => [
            SendNewInquiry::class
        ],
        OfferAccepted::class => [
            SendOfferAccepted::class
        ],
        OfferExpired::class => [
            SendOfferExpired::class
        ],
        OrderCompleted::class => [
            SendOrderCompleted::class
        ],
        ForceOffer::class => [
            SendForcedOffer::class
        ],
        OrderVisibilityCompleted::class => [
            SendVisibilityOrderCompleted::class
        ]
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
