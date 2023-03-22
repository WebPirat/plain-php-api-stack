<?php
require_once __DIR__ . '/classes/Router.php';
// Instantiate the Router class
$router = new Router();

// automatisches Laden aller Routen in deth $router
foreach (glob(__DIR__ . '/routes/*.php') as $file) {
    require_once $file;
}

$router->run();
//handle the request