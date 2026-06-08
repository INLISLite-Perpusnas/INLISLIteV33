<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-pelanggaran', ['namespace' => 'Pelanggaran\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Pelanggaran::index');
	$subroutes->add('index', 'Pelanggaran::index');
	$subroutes->add('detail/(:any)', 'Pelanggaran::detail/$1');
	$subroutes->add('create', 'Pelanggaran::create');
	$subroutes->add('edit/(:any)', 'Pelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'Pelanggaran::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Pelanggaran::apply_status/$1');
	$subroutes->add('do_init', 'Pelanggaran::do_init');
	$subroutes->add('do_upload', 'Pelanggaran::do_upload');
	$subroutes->add('do_delete', 'Pelanggaran::do_delete');
	$subroutes->add('flip', 'Pelanggaran::flip');
});

$routes->group('sirkulasi-pelanggaran', ['namespace' => 'Pelanggaran\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Pelanggaran::index');
	$subroutes->add('index', 'Pelanggaran::index');
	$subroutes->add('detail/(:any)', 'Pelanggaran::detail/$1');
	$subroutes->add('create', 'Pelanggaran::create');
	$subroutes->add('edit/(:any)', 'Pelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'Pelanggaran::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Pelanggaran::apply_status/$1');
	$subroutes->add('do_init', 'Pelanggaran::do_init');
	$subroutes->add('do_upload', 'Pelanggaran::do_upload');
	$subroutes->add('do_delete', 'Pelanggaran::do_delete');
	$subroutes->add('flip', 'Pelanggaran::flip');
});

$routes->group('api/sirkulasi-pelanggaran', ['namespace' => 'Pelanggaran\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Pelanggaran::detail/$1');
	$subroutes->add('create', 'Pelanggaran::create');
	$subroutes->add('edit/(:any)', 'Pelanggaran::edit/$1');
	$subroutes->add('delete/(:any)', 'Pelanggaran::delete/$1');

	//custom
	$subroutes->add('datatable', 'Pelanggaran::datatable');
	$subroutes->add('datatable/(:any)', 'Pelanggaran::datatable/$1');
	$subroutes->add('switch/(:any)', 'Pelanggaran::switch/$1');
});
