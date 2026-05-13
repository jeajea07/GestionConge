<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/auth/logout', 'AuthController::logout');