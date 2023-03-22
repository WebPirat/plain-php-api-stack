<?php
//login routes
$router = new Router();
$router->post('/login', 'LoginController@login');
$router->get('/logout', 'LoginController@logout');
