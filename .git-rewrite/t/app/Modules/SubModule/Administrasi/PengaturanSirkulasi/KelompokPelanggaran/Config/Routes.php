<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-kelompok-pelanggaran', ['namespace' => 'KelompokPelanggaran\Controllers'], function ($subroutes) {
	$subroutes->add('', 'KelompokPelanggaran::index');
	$subroutes->add('index', 'KelompokPelanggaran::index');
	$subroutes->add('detail/(:any)', 'KelompokPelanggaran::detail/$1');
	$subroutes->add('create', 'KelompokPelanggaran::create');
	$subroutes->add('edit/(:any)', 'KelompokPelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'KelompokPelanggaran::delete/$1');
	$subroutes->add('apply_status/(:any)', 'KelompokPelanggaran::apply_status/$1');
	$subroutes->add('do_init', 'KelompokPelanggaran::do_init');
	$subroutes->add('do_upload', 'KelompokPelanggaran::do_upload');
	$subroutes->add('do_delete', 'KelompokPelanggaran::do_delete');
	$subroutes->add('flip', 'KelompokPelanggaran::flip');
});

$routes->group('api/master-kelompok-umur', ['namespace' => 'KelompokPelanggaran\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'KelompokPelanggaran::index');
	$subroutes->add('detail/(:any)', 'KelompokPelanggaran::detail/$1');
	$subroutes->add('create', 'KelompokPelanggaran::create');
	$subroutes->add('edit/(:any)', 'KelompokPelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'KelompokPelanggaran::delete/$1');

	//custom
	$subroutes->add('datatable', 'KelompokPelanggaran::datatable');
	$subroutes->add('datatable/(:any)', 'KelompokPelanggaran::datatable/$1');
});