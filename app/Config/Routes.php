<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root route - redirect to login
$routes->get('/', static function () {
    return redirect()->to('/login');
});

// Authentication routes
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// Admin routes (requires admin role)
$routes->group('admin', ['filter' => 'group:admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::adminDashboard');
});

// Manager routes (requires manager role)
$routes->group('manager', ['filter' => 'group:manager'], function($routes) {
    $routes->get('dashboard', 'DashboardController::managerDashboard');
});

// POS routes (requires cashier role or above)
$routes->group('pos', ['filter' => 'group:cashier,manager,admin'], function($routes) {
    $routes->get('/', 'DashboardController::pos');
});
