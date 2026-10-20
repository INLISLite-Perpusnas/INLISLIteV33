<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('usulankoleksi', ['namespace' => 'UsulanKoleksi\Controllers'], function ($subroutes) {
    $subroutes->get('', 'UsulanKoleksi::index');
    $subroutes->get('index', 'UsulanKoleksi::index');
});
$routes->group('api/usulankoleksi', ['namespace' => 'UsulanKoleksi\Controllers\Api'], function ($subroutes) {
    $subroutes->get('datatable', 'UsulanKoleksi::datatable');
    $subroutes->post('status/(:num)', 'UsulanKoleksi::updateStatus/$1', ['filter' => 'csrf']);
});
