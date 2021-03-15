<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Type relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }
}
