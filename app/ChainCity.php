<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChainCity extends Model
{
    /**
     * @var bool
     */
    public $table = 'chain_city';
    public $guarded = ['id'];
    public $timestamps = false;
}
