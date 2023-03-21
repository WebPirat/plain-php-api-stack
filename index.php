<?php
require_once '/classes/Router.php';

// Callback function for authenticated route
function hello() {
    echo 'Hello, authenticated user!';
}

// Callback function for unauthenticated route
function world() {
    echo 'Hello, world!';
}

// Instantiate the Router class
$router = new Router();

// Add routes
$router->add('/', 'world');
$router->add('/hello', 'hello', 'GET', true);

// Run the router
$router->run();