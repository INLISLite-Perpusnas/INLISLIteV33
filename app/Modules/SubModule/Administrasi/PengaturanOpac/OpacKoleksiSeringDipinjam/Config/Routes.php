<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-koleksi-sering-dipinjam', ['namespace' => 'OpacKoleksiSeringDipinjam\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacKoleksiSeringDipinjam::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacKoleksiSeringDipinjam::index', ['filter' => 'csrf']);
});
