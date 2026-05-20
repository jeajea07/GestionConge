<?php

use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */
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

$routes->group('employe', ['filter' => 'auth:employe'], function($routes) {
    $routes->get('dashboard', 'Employe\DashboardController::index');
    $routes->get('demande', 'Employe\DashboardController::showFormDemandeConge');
    $routes->post('demande', 'Employe\DashboardController::storeDemandeConge');
    $routes->get('conges', 'Employe\DashboardController::showHistoriqueConge');
    $routes->post('conges/cancel/(:num)', 'Employe\DashboardController::cancelDemandeConge/$1');
});

$routes->group('rh', ['filter' => 'auth:rh'], function($routes) {
    $routes->get('dashboard', 'RH\DashboardController::index');
    $routes->get('historique', 'RH\DashboardController::historique');
    $routes->get('soldes', 'RH\DashboardController::soldes');
    $routes->get('soldes/edit/(:num)', 'RH\DashboardController::editSolde/$1');
    $routes->post('soldes/update/(:num)', 'RH\DashboardController::updateSolde/$1');
    $routes->post('conges/approve/(:num)', 'RH\DashboardController::approve/$1');
    $routes->post('conges/refuse/(:num)', 'RH\DashboardController::refuse/$1');
});
