<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-eksemplar', ['namespace' => 'LaporanEksemplar\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanEksemplar::index');
	$subroutes->add('index', 'LaporanEksemplar::index');
	$subroutes->add('export', 'LaporanEksemplar::export');
	$subroutes->add('export_pdf', 'LaporanEksemplar::exportPdf');
	$subroutes->add('preview', 'LaporanEksemplar::preview');
	$subroutes->add('visitor', 'LaporanEksemplar::visitor');
	$subroutes->add('visitor_export', 'LaporanEksemplar::visitor_export');
	$subroutes->add('member', 'LaporanEksemplar::member');
	$subroutes->add('member_export', 'LaporanEksemplar::member_export');
	$subroutes->post('get-ruang', 'LaporanEksemplar::getRuang');
});

$routes->group('api/laporan-eksemplar', ['namespace' => 'LaporanEksemplar\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanEksemplar::index');
	$subroutes->add('index', 'LaporanEksemplar::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanEksemplar::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanEksemplar::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanEksemplar::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanEksemplar::member_datatable/$1');
});
