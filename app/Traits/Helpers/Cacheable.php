<?php

namespace App\Traits\Helpers;

trait Cacheable
{

    /**
     * Get table cache key
     *
     * @return string
     */
    public function cacheKey()
    {
        return sprintf(
            "%s/%s-%s",
            $this->getTable(),
            $this->getKey(),
            $this->updated_at->timestamp
        );
    }
}
