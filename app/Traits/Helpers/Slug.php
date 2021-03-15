<?php

namespace App\Traits\Helpers;

use Illuminate\Support\Str;

trait Slug
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = self::createSlug($model->title);
        });
    }

    public static function createSlug($title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = self::getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }
    }

    protected static function getRelatedSlugs($slug, $id = 0)
    {
        return self::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }
}
