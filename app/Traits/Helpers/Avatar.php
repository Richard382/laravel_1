<?php

namespace App\Traits\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait Avatar
{
    /**
     * Set the avatar image
     *
     * @param  string  $value
     * @return void
     */
    public function setAvatarAttribute($value)
    {
        $path = false;

        if ($value) {

            $newFilename = "avatar-".time().".png";
            $path = 'avatars/' . $newFilename;

            $img = Image::make(file_get_contents($value))->resize(500, 500);

            Storage::disk('public')->put($path, $img->encode());

            $this->attributes['avatar'] = $path;

            return;
        }

        $this->attributes['avatar'] = '';
    }

    /**
     * @return int
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }
}

