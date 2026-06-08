<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('pengaturananggota', ['namespace' => 'Pengaturananggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Pengaturananggota::index');
	$subroutes->add('index', 'Pengaturananggota::index');
	$subroutes->add('detail/(:any)', 'Pengaturananggota::detail/$1');
	$subroutes->add('edit/(:any)', 'Pengaturananggota::edit/$1');
	$subroutes->add('create', 'Pengaturananggota::create');
	$subroutes->add('delete/(:any)', 'Pengaturananggota::delete/$1');
	$subroutes->add('do_init', 'Pengaturananggota::do_init');
	$subroutes->add('do_upload', 'Pengaturananggota::do_upload');
	$subroutes->add('do_delete', 'Pengaturananggota::do_delete');
	$subroutes->add('flip', 'Pengaturananggota::flip');
	$subroutes->add('apply_status/(:any)', 'Pengaturananggota::apply_status/$1');
	$subroutes->add('export', 'Pengaturananggota::export');
	$subroutes->add('thumb', 'Pengaturananggota::thumb');
});

$routes->group('api/pengaturananggota', ['namespace' => 'Pengaturananggota\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Pengaturananggota::index');
	$subroutes->add('index', 'Pengaturananggota::index');
	$subroutes->add('detail/(:any)', 'Pengaturananggota::detail/$1');
	$subroutes->add('show/(:any)', 'Pengaturananggota::show/$1');
	$subroutes->add('create', 'Pengaturananggota::create');
	$subroutes->add('update/(:any)', 'Pengaturananggota::update/$1');
	$subroutes->add('delete/(:any)', 'Pengaturananggota::delete/$1');

	//custom
	$subroutes->add('datatable', 'Pengaturananggota::datatable');
	$subroutes->add('datatable/(:any)', 'Pengaturananggota::datatable/$1');
	$subroutes->add('(:any)', 'Pengaturananggota::detail/$1');
});
