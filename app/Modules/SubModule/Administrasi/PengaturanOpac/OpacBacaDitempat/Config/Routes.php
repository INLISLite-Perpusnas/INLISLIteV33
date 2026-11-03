<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('master-opac-baca-ditempat', ['namespace' => 'OpacBacaDitempat\Controllers'], function ($subroutes) {
    $subroutes->match(['get', 'post'], '/', 'OpacBacaDitempat::index', ['filter' => 'csrf']);
    $subroutes->match(['get', 'post'], 'index', 'OpacBacaDitempat::index', ['filter' => 'csrf']);
});
