<?php
// router class in php

class Router
{
    private $routes = [];
    private $groupPrefix = '';
    private $groupMiddleware = [];

    public function get($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    public function post($route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    public function put($route, $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    public function delete($route, $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    public function group($prefix, $callback)
    {
        $this->groupPrefix = $prefix;
        $callback();
        $this->groupPrefix = '';
    }

    public function middleware($middleware)
    {
        $this->groupMiddleware[] = $middleware;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        $uri = rawurldecode($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace_callback('/{([a-z0-9_]+):([^}]+)}/', function ($matches) {
                return '(?P<' . $matches[1] . '>' . str_replace('\*', '.*', preg_quote($matches[2])) . ')';
            }, $route['route']);

            $pattern = '@^' . $pattern . '$@D';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            $params = [];

            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $params[$key] = $match;
                }
            }

            $handler = $route['handler'];

            if (is_callable($handler)) {
                call_user_func_array($handler, $params);
            } else {
                $handler = explode('@', $handler);
                $controller = new $handler[0]();
                call_user_func_array([$controller, $handler[1]], $params);
            }
        }
    }
}