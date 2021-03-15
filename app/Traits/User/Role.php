<?php

namespace App\Traits\User;

use Illuminate\Support\Facades\Cache;

trait Role
{
    /**
     * Relate model to roles
     *
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo('TCG\Voyager\Models\Role');
    }

    /**
     * Cache role
     *
     * @return mixed
     */
    public function getRole()
    {
        return $this->role()->first();
//        return Cache::rememberForever($this->cacheKey() . ':role', function ()
//        {
//            
//        });
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        if ($this->getRole()->slug === $role)
        {
            return true;
        }

        return false;
    }

    public function isSystemUser()
    {
        try{
            if (! $this->isBroker() && ! $this->isRegular()) {
                return true;
            }
        }
        catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isRegular()
    {
        try{
            if ($this->getRole()->slug === 'regular') {
                return true;
            }
        }catch(\Exception $e) {
            return false;
        }

    }

    /**
     * @return bool
     */
    public function isBroker()
    {
        try {
            if ($this->getRole()->slug === 'broker') {
                return true;
            }

        }catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @param $slug
     * @return $this
     */
    public function become($slug)
    {
        $role = \App\Role::where('slug', $slug)->first();

        $this->role()->associate($role);

        $this->save();

        return $this;
    }
}
