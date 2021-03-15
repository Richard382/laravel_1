<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificates extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url'
    ];

    protected $appends = [
        'url_full'
    ];

    public function getUrlFullAttribute()
    {
        return $this->url ? Storage::url($this->url) : null;
    }
}
