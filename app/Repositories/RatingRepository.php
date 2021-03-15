<?php

namespace App\Repositories;

use App\Company;
use App\Events\NewOffer;
use App\Inquiry;
use App\Offer;
use App\Rating;

class RatingRepository
{
    public static function exists(Company $company, Inquiry $inquiry)
    {
        $rating = Rating::where('company_id', $company->id)->where('inquiry_id', $inquiry->id)->first();

        if ($rating) {
            return true;
        }

        return false;
    }
}
