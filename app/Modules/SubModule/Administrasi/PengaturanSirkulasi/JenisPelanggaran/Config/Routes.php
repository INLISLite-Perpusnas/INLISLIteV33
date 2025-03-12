<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-pelanggaran', ['namespace' => 'JenisPelanggaran\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisPelanggaran::index');
	$subroutes->add('index', 'JenisPelanggaran::index');
	$subroutes->add('detail/(:any)', 'JenisPelanggaran::detail/$1');
	$subroutes->add('create', 'JenisPelanggaran::create');
	$subroutes->add('edit/(:any)', 'JenisPelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPelanggaran::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisPelanggaran::apply_status/$1');
	$subroutes->add('do_init', 'JenisPelanggaran::do_init');
	$subroutes->add('do_upload', 'JenisPelanggaran::do_upload');
	$subroutes->add('do_delete', 'JenisPelanggaran::do_delete');
	$subroutes->add('flip', 'JenisPelanggaran::flip');
});

$routes->group('api/jenis-pelanggaran', ['namespace' => 'JenisPelanggaran\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisPelanggaran::detail/$1');
	$subroutes->add('create', 'JenisPelanggaran::create');
	$subroutes->add('edit/(:any)', 'JenisPelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPelanggaran::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisPelanggaran::datatable');
	$subroutes->add('datatable/(:any)', 'JenisPelanggaran::datatable/$1');
});