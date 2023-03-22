<?php
require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../middlewares/VerifyTokenMiddleware.php';

$router = new Router();
$router->get('/users', 'UserController@index', [new VerifyTokenMiddleware]);
$router->post('/users', 'UserController@store', [new VerifyTokenMiddleware]);
$router->get('/users/{id:\d+}', 'UserController@show', [new VerifyTokenMiddleware]);
$router->put('/users/{id:\d+}', 'UserController@update', [new VerifyTokenMiddleware]);
$router->delete('/users/{id:\d+}', 'UserController@destroy', [new VerifyTokenMiddleware]);

