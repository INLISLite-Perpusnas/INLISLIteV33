<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-usulan-koleksi', ['namespace' => 'OpacUsulanKoleksi\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacUsulanKoleksi::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacUsulanKoleksi::index', ['filter' => 'csrf']);
});
