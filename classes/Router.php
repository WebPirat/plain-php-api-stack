<?php
// router class in php

class Router
{
    private $routes = [];
    private $groupPrefix = '';
    private $groupMiddleware = [];

    public function get($route, $handler, $middleware = [])
    {
        $this->addRoute('GET', $route, $handler, $middleware);
    }

    public function post($route, $handler, $middleware = [])
    {
        $this->addRoute('POST', $route, $handler, $middleware);
    }

    public function put($route, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $route, $handler, $middleware);
    }

    public function delete($route, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $route, $handler, $middleware);
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

    public function verifyToken()
    {
        try {
            $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $matches = [];
            if (preg_match('/^Bearer\s+(.*?)$/', $authorizationHeader, $matches)) {
                $token = $matches[1];
                // Decode the JWT token using the "decode" method of the "JWT" class
                $payload = JWT::decode($token);

                // Verify the payload contains the necessary information
                if (!isset($payload->iss) || $payload->iss !== 'my-app' || !isset($payload->exp)) {
                    throw new Exception('Invalid token');
                }

                // Verify the token expiration time
                $now = time();
                if ($payload->exp <= $now) {
                    throw new Exception('Token has expired');
                }
            }else{
                throw new Exception('Token not found');
            }
        } catch (Exception $e) {
            header('HTTP/1.0 401 Unauthorized');
            exit($e->getMessage());
        }
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
            $middlewares = $route['middlewares'];

            foreach ($middlewares as $middleware) {
                $handler = function ($request) use ($handler, $middleware) {
                    return $middleware->handle($request, $handler);
                };
            }

            if (is_callable($handler)) {
                call_user_func_array($handler, $params);
            } else {
                $handler = explode('@', $handler);
                $controller = new $handler[0]();
                call_user_func_array([$controller, $handler[1]], $params);
            }
        }
    }

    private function addRoute($string, $route, $handler, $middlewares = [])
    {
        $route = $this->groupPrefix . $route;
        $this->routes[] = [
            'method' => $string,
            'route' => $route,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }
}