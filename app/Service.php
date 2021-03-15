<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    const TYPE_OFFER = 'suggest';
    const TYPE_LOOKIN = 'looking';
    const TYPE_OTHER = 'other';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type_user', 'type_broker', 'type'
    ];

    public function scopeTypeUser()
    {
        $collection = $this->all()->map(function ($item, $key) {

            $item->name = $item->type_user;

           return  $item;
        });

        return $collection;
    }

    public function scopeTypeBroker()
    {
        $collection = $this->all()->map(function ($item, $key) {

            $item->name = $item->type_broker;

            return  $item;
        });

        return $collection;
    }
}
