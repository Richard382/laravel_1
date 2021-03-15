<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BrokerVisibilityDurations extends Model
{
    public $timestamps = false;

    public function countFromNow()
    {
        return Carbon::now()->addMonths($this->months);
    }
}
