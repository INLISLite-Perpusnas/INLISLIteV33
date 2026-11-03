<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-faset', ['namespace' => 'OpacFaset\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacFaset::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacFaset::index', ['filter' => 'csrf']);
});
