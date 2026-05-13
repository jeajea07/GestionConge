<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/auth/logout', 'AuthController::logout');

$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardAdminController::index');
    $routes->get('employes', 'Admin\DashboardAdminController::employees');
    $routes->post('employes/store', 'Admin\DashboardAdminController::store');
    $routes->post('employes/update/(:num)', 'Admin\DashboardAdminController::update/$1');
    $routes->post('employes/deactivate/(:num)', 'Admin\DashboardAdminController::deactivate/$1');
    $routes->post('employes/reactivate/(:num)', 'Admin\DashboardAdminController::reactivate/$1');
});
