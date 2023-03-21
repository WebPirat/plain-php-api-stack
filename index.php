<?php
require_once '/classes/Router.php';
// Instantiate the Router class
$router = new Router();

// automatisches Laden aller Routen
foreach (glob(__DIR__ . '/routes/*.php') as $filename) {
    $routeName = basename($filename, '.php');
    $routePath = '/' . $routeName;
    include $filename;
    $router->group($routePath, function () use ($routeName) {
        $this->get('', "{$routeName}@index");
        $this->post('', "{$routeName}@store");
        $this->get('/{id:\d+}', "{$routeName}@show");
        $this->put('/{id:\d+}', "{$routeName}@update");
        $this->delete('/{id:\d+}', "{$routeName}@destroy");
    });
}

$router->run();