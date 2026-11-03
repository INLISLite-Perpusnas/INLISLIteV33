<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('master-opac-logs', ['namespace' => 'OpacLogs\Controllers'], function ($subroutes) {
    $subroutes->match(['get', 'post'], '/', 'OpacLogs::index', ['filter' => 'csrf']);
    $subroutes->match(['get', 'post'], 'index', 'OpacLogs::index', ['filter' => 'csrf']);
});
