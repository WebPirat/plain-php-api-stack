<?php
//use the /classes to build a restapi for users

$router = new Router();
$router->get('/users', 'UserController@index');
$router->post('/users', 'UserController@store');
$router->get('/users/{id:\d+}', 'UserController@show');
$router->put('/users/{id:\d+}', 'UserController@update');
$router->delete('/users/{id:\d+}', 'UserController@destroy');

