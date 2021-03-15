<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChainService extends Model
{
    /**
     * @var bool
     */
    public $table = 'chain_service';
    public $guarded = ['id'];
    public $timestamps = false;
}
