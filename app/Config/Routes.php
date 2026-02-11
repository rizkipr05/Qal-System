<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

$routes->group('dc', function ($routes) {
    $routes->get('/', 'Dc\\Documents::index');
    $routes->get('profile', 'Dc\\Documents::profile');
    $routes->post('profile', 'Dc\\Documents::updateProfile');
    $routes->get('create', 'Dc\\Documents::create');
    $routes->post('store', 'Dc\\Documents::store');
    $routes->get('(:num)', 'Dc\\Documents::show/$1');
    $routes->get('(:num)/print', 'Dc\\Documents::print/$1');
    $routes->get('(:num)/edit', 'Dc\\Documents::edit/$1');
    $routes->post('(:num)/update', 'Dc\\Documents::update/$1');
    $routes->post('(:num)/delete', 'Dc\\Documents::delete/$1');
    $routes->post('(:num)/submit', 'Dc\\Documents::submit/$1');
    $routes->post('(:num)/revision', 'Dc\\Documents::uploadRevision/$1');
    $routes->post('(:num)/review', 'Dc\\Documents::review/$1');
    $routes->post('(:num)/approve', 'Dc\\Documents::approve/$1');
    $routes->post('(:num)/owner-approve', 'Dc\\Documents::ownerApprove/$1');
    $routes->get('version/(:num)/download', 'Dc\\Documents::downloadVersion/$1');
});

$routes->group('admin', function ($routes) {
    $routes->get('users', 'Admin\\Users::index');
    $routes->get('users/create', 'Admin\\Users::create');
    $routes->post('users/store', 'Admin\\Users::store');
    $routes->get('users/(:num)/edit', 'Admin\\Users::edit/$1');
    $routes->post('users/(:num)/update', 'Admin\\Users::update/$1');
    $routes->post('users/(:num)/reset-password', 'Admin\\Users::resetPassword/$1');
    $routes->get('logs', 'Admin\\Users::logs');
});
