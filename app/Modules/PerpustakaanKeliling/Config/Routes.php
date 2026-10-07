<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Routes Frontend Public
$routes->group('perpustakaan-keliling', ['namespace' => 'PerpustakaanKeliling\Controllers'], function ($subroutes) {
    $subroutes->get('', 'PerpustakaanKeliling::index');
    $subroutes->get('(:segment)', 'PerpustakaanKeliling::detail/$1');
});
