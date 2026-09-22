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
	$subroutes->add('browse', 'GuestBook::browse');
	$subroutes->add('detail/(:any)', 'GuestBook::detail/$1');
	$subroutes->add('visitor_export', 'GuestBook::visitor_export');
	$subroutes->add('member', 'GuestBook::member');
	$subroutes->post('store_anggota', 'GuestBook::store_anggota', ['filter' => 'csrf']);
	$subroutes->add('member_export', 'GuestBook::member_export');
});

$routes->group('api/GuestBook', ['namespace' => 'GuestBook\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'GuestBook::index');
	$subroutes->add('index', 'GuestBook::index');

	//custom
	$subroutes->add('visitor_datatable', 'GuestBook::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'GuestBook::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'GuestBook::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'GuestBook::member_datatable/$1');
});
