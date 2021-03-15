<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\Events\NewInquiry;
use App\Http\Controllers\Company\ContactController;
use App\Http\Controllers\Controller;
use App\Inquiry;
use App\User;

class ConfirmUserController extends Controller
{
    public function index($token)
    {
//        $user = User::where('token', '=', $token)->firstOrFail();
        $user = User::where('token', '=', $token)->first();
        if(!$user) {
            $invalid = true;
            return view('auth.verify',compact("invalid"));
        }
        $user->email_verified = true;
        $user->token = null;

        $inquiries = Inquiry::where('user_id', '=', $user->id)->get();

        if (! $inquiries->isEmpty()) {
            foreach ($inquiries as $inquiry) {
                $inquiry->update([
                    'active' => 1
                ]);

                event(new NewInquiry($inquiry));

                if ($inquiry->contact_company) {
                    $comapny = Company::find($inquiry->contact_company);
                    ContactController::connect($comapny, $inquiry);
                }
            }
        }

        $user->save();
//        \Auth::login($user);
        return view('auth.verify');
    }
}
