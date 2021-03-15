<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ChainCity;

class Chain extends Model
{
    /**
     * @var bool
     */
    public $table = 'chain';
    public $guarded = ['id'];
    
    public function cities()
    {
        return $this->belongsToMany(ChainCity::class);
    }
}
