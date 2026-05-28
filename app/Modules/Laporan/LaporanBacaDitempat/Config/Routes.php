<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-baca-ditempat', ['namespace' => 'LaporanBacaDitempat\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanBacaDitempat::index');
	$subroutes->add('index', 'LaporanBacaDitempat::index');
	$subroutes->add('export', 'LaporanBacaDitempat::export');
	$subroutes->add('export_pdf', 'LaporanBacaDitempat::exportPdf');
	$subroutes->add('preview', 'LaporanBacaDitempat::preview');
});
