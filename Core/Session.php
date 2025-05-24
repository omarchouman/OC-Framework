<?php

namespace Core;

class Session
{
    /**
     * @param $key
     * @return bool
     */
    public static function has($key): bool
    {
        return (bool) static::get($key);
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public static function put($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public static function get($key, $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public static function flash($key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * @return void
     */
    public static function unflash(): void
    {
        unset($_SESSION['_flash']);
    }

    /**
     * @return void
     */
    public static function flush(): void
    {
        $_SESSION = [];
    }

    /**
     * @return void
     */
    public static function destroy(): void
    {
        static::flush();

        session_destroy();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}