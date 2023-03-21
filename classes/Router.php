<?php
class Router {
    private $routes = array();

    public function add($pattern, $callback, $method = 'GET', $auth = false) {
        $this->routes[] = array(
            'pattern' => $pattern,
            'callback' => $callback,
            'method' => $method,
            'auth' => $auth
        );
    }

    public function get($pattern, $callback) {
        $this->add($pattern, $callback, 'GET');
    }

    public function post($pattern, $callback) {
        $this->add($pattern, $callback, 'POST');
    }

    public function put($pattern, $callback) {
        $this->add($pattern, $callback, 'PUT');
    }

    public function delete($pattern, $callback) {
        $this->add($pattern, $callback, 'DELETE');
    }

    public function run() {
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_method = $_SERVER['REQUEST_METHOD'];
        $found_route = false;
        $authenticated = false;

        foreach($this->routes as $route) {
            $pattern = $route['pattern'];
            $callback = $route['callback'];
            $method = $route['method'];
            $auth = $route['auth'];

            if($request_method != $method) {
                continue;
            }

            if(preg_match("#^$pattern$#", $request_uri, $matches)) {
                array_shift($matches);

                // Check if authentication is required
                if($auth && !$authenticated) {
                    header('WWW-Authenticate: Basic realm="Restricted Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo 'Authentication required';
                    exit;
                }

                // If authentication is not required or authentication succeeded, call the callback function
                call_user_func_array($callback, $matches);
                $found_route = true;
                break;
            }
        }

        if(!$found_route) {
            header("HTTP/1.0 404 Not Found");
        }
    }
}