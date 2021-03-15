<?php

namespace App\Repositories;

use App\Company;
use App\Events\NewOffer;
use App\Inquiry;
use App\Offer;

class OfferRepository extends Offer
{
    public static function create($data, $status, Company $company, Inquiry $inquiry)
    {
        $offer = Offer::create($data);

        $offer->company()->associate($company);
        $offer->inquiry()->associate($inquiry);

        if ($status === Offer::STATUS_ACCEPTED)
        {
            $offer->accept();
        } elseif ($status === Offer::STATUS_DECLINED)
        {
            $offer->decline();
        } else
        {
            $offer->status = $status;

            event(new NewOffer($offer, $inquiry));
        }

        $offer->save();

        return $offer;
    }
}
