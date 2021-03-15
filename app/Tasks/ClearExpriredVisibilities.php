<?php

namespace App\Tasks;

use App\BrokerVisibility;
use App\Events\OfferExpired;
use App\Events\VisibilityExpired;
use Illuminate\Support\Facades\Log;

class ClearExpriredVisibilities
{
    public function __construct()
    {
        $visibilities = BrokerVisibility::expired()->get();

        if ( ! $visibilities->count())
        {
            Log::info('No expired visibilities!');

            return;
        }

        foreach ($visibilities as $visibility)
        {
            $visibility->decline();

            event(new VisibilityExpired($visibility));
        }

        Log::info('Expired visibilities cleared!');

        return;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}
