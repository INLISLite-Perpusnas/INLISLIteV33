<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('buku-tamu', ['namespace' => 'GuestBook\Controllers'], function ($subroutes) {
	$subroutes->get('', 'GuestBook::index');
	$subroutes->get('index', 'GuestBook::index');
	$subroutes->get('lokasi', 'GuestBook::lokasi');
	$subroutes->post('lokasi', 'GuestBook::lokasi', ['filter' => 'csrf']);
	$subroutes->get('non_anggota', 'GuestBook::non_anggota');
	$subroutes->post('non_anggota', 'GuestBook::non_anggota', ['filter' => 'csrf']);
	$subroutes->get('rombongan', 'GuestBook::rombongan');
	$subroutes->post('rombongan', 'GuestBook::rombongan', ['filter' => 'csrf']);
	$subroutes->post('store_anggota', 'GuestBook::store_anggota', ['filter' => 'csrf']);
});
