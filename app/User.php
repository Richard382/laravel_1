<?php

namespace App;

use App\Repositories\InquiryRepository;
use App\Traits\Helpers\Avatar;
use App\Traits\Helpers\Cacheable;
use App\Traits\User\Broker;
use App\Traits\User\Regular;
use App\Traits\User\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable, Broker, Regular, Avatar, Role, Cacheable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'company_id', 'avatar', 'password', 'token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'avatar_url'
    ];

    /**
     * Get user inquiry count
     *
     * @cached 10mins
     * @return mixed
     */
    public function getInquiryCount()
    {
        return Cache::remember($this->cacheKey() . ':spheres', 10, function (){
            return InquiryRepository::getAvailable()->count();
        });
    }

    public function oneSignalPlayers()
    {
        return $this->hasMany(OneSignalPlayers::class);
    }

    /**
     * @return string
     */
    public function routeNotificationForOneSignal()
    {
        return $this->oneSignalPlayers()->pluck('player_id')->toArray();
    }

    public function getConfirmationLink()
    {
        return route('register.confirmation', ['token' => $this->token]);
    }
    public function isAllView() {
        if(\Auth::check()){
            return \DB::table('notifications')->where("notifiable_id",\Auth::id())
                    ->where('isview', 0)
                    ->whereNull('read_at')
                    ->count();
        }else{
            return 0;
        }
    }
}
