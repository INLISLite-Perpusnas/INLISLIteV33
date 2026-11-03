<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-koleksi-terbaru', ['namespace' => 'OpacKoleksiTerbaru\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacKoleksiTerbaru::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacKoleksiTerbaru::index', ['filter' => 'csrf']);
});
