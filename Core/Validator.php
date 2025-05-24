<?php

namespace Core;

class Validator
{
    /**
     * @param $value
     * @param  int  $min
     * @param  float  $max
     * @return bool
     */
    public static function string($value, int $min = 1, float $max = INF): bool
    {
        $value = trim($value);

        return strlen($value) >= $min && strlen($value) <= $max;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function email($value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}