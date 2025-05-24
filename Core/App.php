<?php

namespace Core;

class App
{
    protected static $container;

    /**
     * @param $container
     * @return void
     */
    public static function setContainer($container): void
    {
        static::$container = $container;
    }

    /**
     * @return mixed
     */
    public static function container(): mixed
    {
        return static::$container;
    }

    /**
     * @param $key
     * @param $resolver
     * @return void
     */
    public static function bind($key, $resolver): void
    {
        static::container()->resolve($key, $resolver);
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function resolve($key): mixed
    {
        return static::container()->resolve($key);
    }
}