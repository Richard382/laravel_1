<?php

namespace App\Http\Controllers\Offer;

use App\Events\OfferAccepted;
use App\Inquiry;
use App\Offer;
use App\Repositories\InquiryRepository;

class Controller extends \App\Http\Controllers\Controller
{
    public function accept(Offer $offer, $inquiryID)
    {
        $inquiry = InquiryRepository::getInquiryById($inquiryID);

        // Check weather inquiry is available and is same as offer has
        if ( ! $inquiry || $inquiry->id != $offer->inquiry->id)
        {
            return response()->json([
                'message' => 'Susisiekimas negalimas!',
            ], 404);
        }

        if ($offer->accepted)
        {
            return response()->json([
                'message' => 'Jūs jau susisiekėte su šiuo brokeriu, laukite atsakymo.',
            ], 401);
        }

        if ( ! $offer->inquiry->active)
        {
            return response()->json([
                'message' => 'Vienas iš brokerių apmokėjo užklausa, laukite atsakymo.',
            ], 401);
        }

        $offer->accept();

        event(new OfferAccepted($offer, $inquiry));

        return response()->json([
            'message' => 'Jūsų kontaktai išsiųsti paslaugos teikėjui'
        ], 201);
    }
}
