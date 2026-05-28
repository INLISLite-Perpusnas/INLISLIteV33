<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-anggota', ['namespace' => 'LaporanAnggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanAnggota::index');
	$subroutes->add('index', 'LaporanAnggota::index');
	$subroutes->add('export', 'LaporanAnggota::export');
	$subroutes->add('export_pdf', 'LaporanAnggota::exportPdf');
	$subroutes->add('preview', 'LaporanAnggota::preview');
	$subroutes->add('member', 'LaporanAnggota::member');
	$subroutes->add('member_export', 'LaporanAnggota::member_export');
});

$routes->group('api/laporan-anggota', ['namespace' => 'LaporanAnggota\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanAnggota::index');
	$subroutes->add('index', 'LaporanAnggota::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanAnggota::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanAnggota::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanAnggota::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanAnggota::member_datatable/$1');
});
