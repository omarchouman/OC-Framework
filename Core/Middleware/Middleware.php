<?php

namespace Core\Middleware;

class Middleware
{
    public const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class,
    ];

    public static function resolve($key)
    {
        if(!$key) {
            return;
        }

        $middlware = static::MAP[$key] ?? false;

        if(!$middlware) {
            throw new \Exception("Middleware [$key] not found");
        }

        (new $middlware)->handle();
    }
}