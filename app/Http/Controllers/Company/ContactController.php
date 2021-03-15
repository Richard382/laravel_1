<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Events\ForceOffer;
use App\Inquiry;
use App\Offer;
use App\Repositories\OfferRepository;
use Illuminate\Support\Facades\Auth;

class ContactController extends \App\Http\Controllers\Controller
{
    public static function connect(Company $company, Inquiry $inquiry)
    {
        if ($inquiry->hasOfferFrom($company)) {
            return response()->json([
                'message' => 'Jūs jau susisiekėte su šiuo brokeriu, laukite atsakymo.'
            ], 401);
        }

        if (! $inquiry->active) {
            return response()->json([
                'message' => 'Užklausa nebėra aktyvi.'
            ], 401);
        }

        if ($company->isOwner()) {
            return response()->json([
                'message' => 'Negalite susisiekti su savimi!'
            ], 401);
        }

        $offer = OfferRepository::create([
            'text' => 'Automatškai sugeneruotas pasiulymas'
        ], Offer::STATUS_ACCEPTED, $company, $inquiry);

        event(new ForceOffer($offer, $inquiry));

        return response()->json([
            'message' => 'Jūsų kontaktai išsiųsti paslaugos teikėjui',
            'redirect' => $inquiry->getLink()
        ], 200);
    }
}
