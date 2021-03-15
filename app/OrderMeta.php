<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Psy\Util\Str;

class OrderMeta extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_meta_key',
        'order_meta_value',
    ];

    public function scopeField($query, String $key)
    {
        return $query->where('order_meta_key', $key)->first()->order_meta_value;
    }
}
