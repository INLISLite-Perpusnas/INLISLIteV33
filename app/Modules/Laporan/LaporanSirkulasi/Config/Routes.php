<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-sirkulasi', ['namespace' => 'LaporanSirkulasi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanSirkulasi::index');
	$subroutes->add('index', 'LaporanSirkulasi::index');
	$subroutes->add('export', 'LaporanSirkulasi::export');
	$subroutes->add('preview', 'LaporanSirkulasi::preview');
	// $subroutes->add('visitor', 'LaporanSirkulasi::visitor');
	// $subroutes->add('visitor_export', 'LaporanSirkulasi::visitor_export');
	// $subroutes->add('member', 'LaporanSirkulasi::member');
	// $subroutes->add('member_export', 'LaporanSirkulasi::member_export');
});

$routes->group('api/laporan-sirkulasi', ['namespace' => 'LaporanSirkulasi\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanSirkulasi::index');
	$subroutes->add('index', 'LaporanSirkulasi::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanSirkulasi::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanSirkulasi::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanSirkulasi::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanSirkulasi::member_datatable/$1');
});
