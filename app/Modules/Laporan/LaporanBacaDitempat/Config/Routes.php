<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('laporan-baca-ditempat', ['namespace' => 'LaporanBacaDitempat\Controllers'], function ($subroutes) {
	$subroutes->add('', 'LaporanBacaDitempat::index');
	$subroutes->add('index', 'LaporanBacaDitempat::index');
	$subroutes->add('export', 'LaporanBacaDitempat::export');
	$subroutes->add('preview', 'LaporanBacaDitempat::preview');
	$subroutes->add('visitor', 'LaporanBacaDitempat::visitor');
	$subroutes->add('visitor_export', 'LaporanBacaDitempat::visitor_export');
	$subroutes->add('member', 'LaporanBacaDitempat::member');
	$subroutes->add('member_export', 'LaporanBacaDitempat::member_export');
});

$routes->group('api/laporan-baca-ditempat', ['namespace' => 'LaporanBacaDitempat\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'LaporanBacaDitempat::index');
	$subroutes->add('index', 'LaporanBacaDitempat::index');

	//custom
	$subroutes->add('visitor_datatable', 'LaporanBacaDitempat::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanBacaDitempat::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'LaporanBacaDitempat::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'LaporanBacaDitempat::member_datatable/$1');
});
