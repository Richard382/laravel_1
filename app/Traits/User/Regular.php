<?php

namespace App\Traits\User;

trait Regular
{
    public function inquiries()
    {
        return $this->hasMany('App\Inquiry');
    }
}
