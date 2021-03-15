<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Subobject extends Model
{
	protected $fillable = [
        'object', 'name'
    ];
}
