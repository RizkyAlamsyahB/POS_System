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
    
    // Outlet Management
    $routes->get('outlets', 'Admin\OutletController::index');
    $routes->get('outlets/datatable', 'Admin\OutletController::datatable');
    $routes->get('outlets/create', 'Admin\OutletController::create');
    $routes->post('outlets/store', 'Admin\OutletController::store');
    $routes->get('outlets/view/(:num)', 'Admin\OutletController::view/$1');
    $routes->get('outlets/edit/(:num)', 'Admin\OutletController::edit/$1');
    $routes->post('outlets/update/(:num)', 'Admin\OutletController::update/$1');
    $routes->post('outlets/delete/(:num)', 'Admin\OutletController::delete/$1');
    
    // User Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/datatable', 'Admin\UserController::datatable');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
    $routes->post('users/toggle-status/(:num)', 'Admin\UserController::toggleStatus/$1');
    
    // Category Management
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->get('categories/datatable', 'Admin\CategoryController::datatable');
    $routes->get('categories/create', 'Admin\CategoryController::create');
    $routes->post('categories/store', 'Admin\CategoryController::store');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'Admin\CategoryController::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');
    
    // Product Management
    $routes->get('products', 'Admin\ProductController::index');
    $routes->get('products/create', 'Admin\ProductController::create');
    $routes->post('products/store', 'Admin\ProductController::store');
    $routes->get('products/edit/(:num)', 'Admin\ProductController::edit/$1');
    $routes->post('products/update/(:num)', 'Admin\ProductController::update/$1');
    $routes->post('products/delete/(:num)', 'Admin\ProductController::delete/$1');
    $routes->get('products/stock/(:num)', 'Admin\ProductController::stock/$1');
    $routes->post('products/stock/update/(:num)', 'Admin\ProductController::updateStock/$1');
    $routes->get('products/datatable', 'Admin\ProductController::datatable');
});

// Manager routes (requires manager role + outlet active check)
// Temporarily disable outletactive filter for testing
$routes->group('manager', ['filter' => 'group:manager'], function($routes) {
    $routes->get('dashboard', 'DashboardController::managerDashboard');
    
    // Outlet Management (View Own Outlet Only)
    $routes->get('outlets', 'Manager\OutletController::index');
    
    // Product Management (Read-only + Stock Update)
    $routes->get('products', 'Manager\ProductController::index');
    $routes->get('products/view/(:num)', 'Manager\ProductController::view/$1');
    $routes->get('products/stock', 'Manager\ProductController::stock');
    $routes->post('products/stock/update', 'Manager\ProductController::updateStock');
});

// POS routes (requires cashier role or above + outlet active check)
// Temporarily disable outletactive filter for testing
$routes->group('', ['filter' => 'group:admin,manager,cashier'], function($routes) {
    $routes->get('pos', 'DashboardController::pos');
});
$routes->group('pos', ['filter' => 'group:cashier,manager,admin'], function($routes) {
    $routes->get('/', 'DashboardController::pos');
});
