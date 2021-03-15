<?php

namespace App\Tasks;

use App\Events\OfferExpired;
use App\Offer;
use Illuminate\Support\Facades\Log;

class ClearOffers
{
    public function __construct()
    {
        $offers = Offer::expiredAccepts()->get();

        if ( ! $offers->count())
        {
            Log::info('No expired offers!');

            return;
        }

        foreach ($offers as $offer)
        {
            $offer->decline();
            $offer->company->addPenalty($offer->inquiry);

            event(new OfferExpired($offer));
        }

        Log::info('Expired offers cleared!');

        return;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}
