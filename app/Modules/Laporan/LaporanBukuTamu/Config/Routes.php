<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-buku-tamu', ['namespace' => 'LaporanBukuTamu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanBukuTamu::index');
	$subroutes->add('index', 'LaporanBukuTamu::index');
	$subroutes->add('export', 'LaporanBukuTamu::export');
	$subroutes->add('preview', 'LaporanBukuTamu::preview');
	$subroutes->add('visitor', 'LaporanBukuTamu::visitor');
	$subroutes->add('visitor_export', 'LaporanBukuTamu::visitor_export');
	$subroutes->add('member', 'LaporanBukuTamu::member');
	$subroutes->add('member_export', 'LaporanBukuTamu::member_export');
});

$routes->group('api/laporan-buku-tamu', ['namespace' => 'LaporanBukuTamu\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanBukuTamu::index');
	$subroutes->add('index', 'LaporanBukuTamu::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanBukuTamu::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanBukuTamu::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanBukuTamu::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanBukuTamu::member_datatable/$1');
});
