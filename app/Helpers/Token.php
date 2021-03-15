<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Token
{
    private static $length = 30;

    /**
     * @return int
     */
    private static function getLength(): int
    {
        return self::$length;
    }

    /**
     * Generate unique token
     *
     * @return string
     * @throws \Exception
     */
    public static function generate()
    {
        $token = bin2hex(random_bytes(self::getLength()));

        return $token;
    }
}
