<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-ai', ['namespace' => 'LaporanAI\Controllers'], function ($subroutes) {
	  $subroutes->add('/', 'LaporanAI::index');
    $subroutes->add('process-query', 'LaporanAI::processQuery');
    $subroutes->add('export-excel', 'LaporanAI::exportExcel');
    $subroutes->add('export-pdf', 'LaporanAI::exportPDF');
});

$routes->group('api/laporan-anggota', ['namespace' => 'LaporanAI\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanAI::index');
	$subroutes->add('index', 'LaporanAI::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanAI::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanAI::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanAI::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanAI::member_datatable/$1');
});
