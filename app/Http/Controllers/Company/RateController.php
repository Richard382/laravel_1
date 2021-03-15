<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Inquiry;
use App\Rating;
use App\Repositories\InquiryRepository;
use App\Repositories\RatingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RateController extends \App\Http\Controllers\Controller
{
    public function view(Company $company, $inquiry)
    {
        $inquiry = InquiryRepository::getInquiryById($inquiry);

        if (RatingRepository::exists($company, $inquiry)) {
            abort(404);
        }

        return view('companies.rate', [
            'company' => $company,
            'inquiry' => $inquiry
        ]);
    }

    public function rate(Request $request, Company $company, $inquiry)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        $inquiry = InquiryRepository::getInquiryById($inquiry);

        if (RatingRepository::exists($company, $inquiry)) {
            return response()->json([
                'message' => 'Įmonė jau buvo įvertinta!',
            ], 401);
        }

        $this->create($request->all(), $company, $inquiry);

        $inquiry->setStatus(Inquiry::STATUS_REVIEWED);

        return response()->json([
            'message' => 'Sekmingai pateiktas įvertinimas! Uždaromas Užklausa!',
            'redirect' => route('inquiry.my')
        ], 200);
    }

    public function create(array $data, Company $company, Inquiry $inquiry)
    {
        $rate = Rating::create([
            'rating' => array_sum([$data['speed'], $data['price'], $data['quality'], $data['communication']]) / 4,
            'text' => $data['comment'],
        ]);

        $rate->inquiry()->associate($inquiry);
        $rate->company()->associate($company);

        $rate->save();

        //$inquiry->deactivate();

        return $rate;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'speed'         => 'required|numeric|in:1,1.5,2,2.5,3,3.5,4,4.5,5',
            'price'         => 'required|numeric|in:1,1.5,2,2.5,3,3.5,4,4.5,5',
            'quality'       => 'required|numeric|in:1,1.5,2,2.5,3,3.5,4,4.5,5',
            'communication' => 'required|numeric|in:1,1.5,2,2.5,3,3.5,4,4.5,5',
            'comment'       => 'required|max:300',
        ]);
    }
}
