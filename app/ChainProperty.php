<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChainProperty extends Model
{
    /**
     * @var bool
     */
    public $table = 'chain_properties';
    public $guarded = ['id'];
    public $timestamps = false;
}
