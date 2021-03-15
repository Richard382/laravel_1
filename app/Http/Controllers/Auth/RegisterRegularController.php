<?php

namespace App\Http\Controllers\Auth;

use App\City;
use App\PropertyType;
use App\Repositories\UserRepository;
use App\User;
use App\Language;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterRegularController extends RegisterController
{
    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'             => ['required', 'regex:/^(\+370)\d{8}$/i'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'agreement'         => ['required', 'boolean']
        ];

        return Validator::make($data, $rules, [
            'email.unique' => 'Šis el. paštas jau yra naudojamas.'
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.regular.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Pateikti duomenys buvo neteisingi.'
            ], 422);
        }

        event(new Registered($user = $this->create($request->all())));

        return response()->json([
            'redirect' => route('login'),
            'message' => 'Vartotojas sėkmingai sukurta! Netrukus gausite el. laišką iš CONT (patikrinkite brukalo aplanką) ir patvirtinkite savo paskyrą.',
        ], 200);
    }

    /**
     * @param array $data
     * @return \App\User|void
     */
    public function create(array $data)
    {
        $user = (new UserRepository())->create($data);

        $user->become('regular');

        return $user;
    }
}
