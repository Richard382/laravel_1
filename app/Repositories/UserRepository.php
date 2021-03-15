<?php

namespace App\Repositories;

use App\Notifications\ConfirmAccountEmail;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    protected $model = User::class;

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $data = collect($data);

        $user = $this->model::create([
            'name' => $data->get('name'),
            'email' => $data->get('email'),
            'avatar' => $data->get('avatar', null),
            'phone' => $data->get('phone', null),
            'password' => Hash::make($data->get('password')),
            'token' => Str::random(60)
        ]);

        $user->notify(new ConfirmAccountEmail());

        return $user;
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data)
    {
        $data = collect($data);

        $userData = [
            'name' => $data->get('name', $user->name),
            'email' => $data->get('email', $user->email),
            'phone' => $data->get('phone', $user->phone),
//            'password' => Hash::make($data->get('password')),
        ];
        if($data->get('password')){
           $userData['password'] = Hash::make($data->get('password'));
        }

        if ($data->has('avatar')) {
            $userData['avatar'] = $data->get('avatar', $user->avatar);
        }

        $user->update($userData);

        return $user;
    }
}
