<?php

namespace App\Http\Controllers\Offer;

use App\Company;
use App\Events\NewOffer;
use App\Inquiry;
use App\Offer;
use App\Repositories\OfferRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends \App\Http\Controllers\Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'price' => 'required|string|max:150',
            'offer' => 'required|string|max:300',
            'inquiry_id' => 'required|integer|exists:inquiries,id',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        $inquiry = Inquiry::find($request->get('inquiry_id'));

        if ($inquiry->made_an_offer || $inquiry->offer_declined)
        {
            return response()->json([
                'message' => 'Jūs nebegalite pateikti pasiulymų šiam užklausai!',
            ], 423);
        }

        if ($inquiry->owner)
        {
            return response()->json([
                'message' => 'Jūs negalite pateikti pasiulymų savo užklausai!',
            ], 423);
        }

        $this->create($request->all(), $inquiry);

        // Add redirect which redirects visitor to tokenized link and authenticated to authenticated
        return response()->json([
            'redirect' => route('inquiry.index'),
//            'message' => 'Pasiūlymas sėkmingai sukurta!',
            'message' => 'Užklausa sėkmingai sukurta!',
        ], 201);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    private function create(array $data, Inquiry $inquiry)
    {
        $company = Company::find(Auth::user()->company->id);

        $offer = OfferRepository::create([
            'price' => $data['price'],
            'text' => $data['offer']
        ], Offer::STATUS_DEFAULT, $company, $inquiry);

        return $offer;
    }
}
