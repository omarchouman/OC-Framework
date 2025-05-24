<?php

namespace Core;

use Exception;

class Container
{
    protected array $bindings = [];

    public function bind($key, $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function resolve($key): mixed
    {
        if(!array_key_exists($key, $this->bindings)) {
            throw new Exception("No such key: {$key}");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }
}