<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-katalog', ['namespace' => 'LaporanKatalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanKatalog::index');
	$subroutes->add('index', 'LaporanKatalog::index');
	$subroutes->add('export', 'LaporanKatalog::export');
	$subroutes->add('export_pdf', 'LaporanKatalog::exportPdf');
	$subroutes->add('preview', 'LaporanKatalog::preview');
	$subroutes->add('visitor', 'LaporanKatalog::visitor');
	$subroutes->add('visitor_export', 'LaporanKatalog::visitor_export');
	$subroutes->add('member', 'LaporanKatalog::member');
	$subroutes->add('member_export', 'LaporanKatalog::member_export');
});

$routes->group('api/laporan-katalog', ['namespace' => 'LaporanKatalog\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanKatalog::index');
	$subroutes->add('index', 'LaporanKatalog::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanKatalog::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanKatalog::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanKatalog::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanKatalog::member_datatable/$1');
});
