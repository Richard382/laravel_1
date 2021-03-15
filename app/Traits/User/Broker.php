<?php

namespace App\Traits\User;

use Illuminate\Support\Facades\Cache;

trait Broker
{
    public function getCompanyId()
    {
        return $this->getCompany()->id;
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    /**
     * Get current user company
     *
     * @return mixed
     */
    public function getCompany()
    {
        return Cache::rememberForever($this->cacheKey() . ':company', function (){
            return $this->company()->first();
        });
    }
}
