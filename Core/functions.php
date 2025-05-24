<?php

use Core\Response;
use JetBrains\PhpStorm\NoReturn;
use Support\Env;

if(! function_exists('dd')) {
    #[NoReturn] function dd($value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";

        die();
    }
}

if(! function_exists('urlIs')) {
    /**
     * @param $value
     * @return bool
     */
    function urlIs($value): bool
    {
        return $_SERVER['REQUEST_URI'] === $value;
    }
}

if(! function_exists('abort')) {
    /**
     * @param  int  $code
     * @return void
     */
    #[NoReturn] function abort(int $code = 404): void
    {
        http_response_code($code);

        require base_path("views/{$code}.php");

        die();
    }
}

if(! function_exists('authorize')) {
    /**
     * @param $condition
     * @param  int  $status
     * @return void
     */
    function authorize($condition, int $status = Response::FORBIDDEN): void
    {
        if (! $condition) {
            abort($status);
        }
    }
}

if(! function_exists('base_path')) {
    /**
     * @param $path
     * @return string
     */
    function base_path($path): string
    {
        return BASE_PATH . $path;
    }
}

if(! function_exists('view')) {
    /**
     * @param $path
     * @param $attributes
     * @return void
     */
    function view($path, $attributes = []): void
    {
        extract($attributes);
        require base_path('views/' . $path);
    }
}

if(! function_exists('redirect')) {
    /**
     * @param $path
     * @return void
     */
    #[NoReturn] function redirect($path): void
    {
        header("location: {$path}");
        exit();
    }
}

if(! function_exists('old')) {
    /**
     * @param $key
     * @param  string  $default
     * @return mixed|string
     */
    function old($key, string $default = ''): mixed
    {
        return Core\Session::get('old')[$key] ?? $default;
    }
}


if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}
