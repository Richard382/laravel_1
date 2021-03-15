<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChainPropertyType extends Model
{
    /**
     * @var bool
     */
    public $table = 'chain_property_type';
    public $guarded = ['id'];
    public $timestamps = false;
}
