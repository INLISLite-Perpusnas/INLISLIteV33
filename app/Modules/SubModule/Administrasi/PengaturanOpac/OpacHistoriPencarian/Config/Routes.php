<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-histori-pencarian', ['namespace' => 'OpacHistoriPencarian\Controllers'], function ($subroutes) {
	$subroutes->get('/', 'OpacHistoriPencarian::index');
	$subroutes->get('index', 'OpacHistoriPencarian::index');
});

$routes->group('api/master-opac-histori-pencarian', ['namespace' => 'OpacHistoriPencarian\Controllers\Api'], function ($subroutes) {
	$subroutes->get('datatable', 'OpacHistoriPencarian::datatable');
});
