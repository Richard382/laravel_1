<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Objects Taxo relationships
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)
            ->select("properties.*",
                \DB::raw('if(properties.customer_display_name = "", properties.name, properties.customer_display_name) as name')
            );
    }
}
