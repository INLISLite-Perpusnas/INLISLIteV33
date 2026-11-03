<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

// Daftar Koleksi Yang Dipesan (booking dari OPAC), setara sirkulasi/koleksi-dipesan INLISLite 3
$routes->group('sirkulasi-pemesanan', ['namespace' => 'Pemesanan\Controllers'], function ($subroutes) {
	$subroutes->get('', 'Pemesanan::index');
	$subroutes->get('index', 'Pemesanan::index');
	$subroutes->post('delete', 'Pemesanan::delete', ['filter' => 'csrf']);
});

$routes->group('api/sirkulasi-pemesanan', ['namespace' => 'Pemesanan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('datatable', 'Pemesanan::datatable');
});
