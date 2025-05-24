<?php

namespace Core;

use Core\Middleware\Middleware;
use Exception;

class Router
{
    protected array $routes = [];

    protected array $groupStack = [];

    /**
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function get($uri, $controller): static
    {
        return $this->add('GET', $uri, $controller);
    }

    /**
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function post($uri, $controller): static
    {
        return $this->add('POST', $uri, $controller);
    }

    /**
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function delete($uri, $controller): static
    {
        return $this->add('DELETE', $uri, $controller);
    }

    /**
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function patch($uri, $controller): static
    {
        return $this->add('PATCH', $uri, $controller);
    }

    /**
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function put($uri, $controller): static
    {
        return $this->add('PUT', $uri, $controller);
    }

    /**
     * @param $method
     * @param $uri
     * @param $controller
     * @return $this
     */
    public function add($method, $uri, $controller): static
    {
        $uri = $this->prefix() . trim($uri, '/');
        $middleware = $this->middlewareFromGroup();

        $this->routes[] = [
            'uri' => '/' . trim($uri, '/'),
            'controller' => $controller,
            'method' => strtoupper($method),
            'middleware' => $middleware,
        ];

        return $this;
    }

    /**
     * @param $key
     * @return void
     */
    public function only($key): void
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
    }

    /**
     * @param  string|array  $middleware
     * @return $this
     */
    public function middleware(string|array $middleware): static
    {
        $index = array_key_last($this->routes);
        $this->routes[$index]['middleware'] = $middleware;
        return $this;
    }

    /**
     * @param  array  $attributes
     * @param  callable  $callback
     * @return void
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;

        $callback($this);

        array_pop($this->groupStack);
    }

    /**
     * @return string
     */
    protected function prefix(): string
    {
        $prefix = '';

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= trim($group['prefix'], '/') . '/';
            }
        }

        return $prefix;
    }

    /**
     * @return mixed|null
     */
    protected function middlewareFromGroup(): mixed
    {
        foreach (array_reverse($this->groupStack) as $group) {
            if (isset($group['middleware'])) {
                return $group['middleware'];
            }
        }

        return null;
    }

    /**
     * @param $uri
     * @param $method
     * @return mixed|void
     * @throws Exception
     */
    public function route($uri, $method)
    {
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                $controller = $route['controller'];

                if (is_callable($controller)) {
                    return $controller(); // Run closure
                }

                return require base_path('Http/controllers/' . $controller);
            }
        }

        abort();
    }

    /**
     * @return mixed|string
     */
    public function previousUrl(): mixed
    {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }
}
