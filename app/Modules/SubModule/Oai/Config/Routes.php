<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('oai', ['namespace' => 'Oai\Controllers'], function (
    $subroutes
) {
    $subroutes->add('', 'Oai::index');
    $subroutes->add('index', 'Oai::index');
});
