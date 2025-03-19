<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-penomoran-koleksi', ['namespace' => 'PenomoranKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PenomoranKoleksi::index');
	$subroutes->add('index', 'PenomoranKoleksi::index');
});
