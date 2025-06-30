<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Dashboard22::index', ['namespace' => 'Dashboard\Controllers']);
$routes->get('/', 'home::index', ['namespace' => 'Home\Controllers']);
$routes->post('/authlogin', 'Home::index', ['namespace' => 'App\Controllers']);
$routes->add('/home/encrypt', 'Home::encrypt', ['namespace' => 'App\Controllers']);
$routes->add('/home/decrypt', 'Home::decrypt', ['namespace' => 'App\Controllers']);
