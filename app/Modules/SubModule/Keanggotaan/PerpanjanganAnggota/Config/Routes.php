<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('perpanjangan-anggota', ['namespace' => 'PerpanjanganAnggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PerpanjanganAnggota::index');
	$subroutes->add('index', 'PerpanjanganAnggota::index');
	$subroutes->add('detail/(:any)', 'PerpanjanganAnggota::detail/$1');
	$subroutes->add('create', 'PerpanjanganAnggota::create');
	$subroutes->add('edit/(:any)', 'PerpanjanganAnggota::edit/$1');
	$subroutes->add('delete/(:any)', 'PerpanjanganAnggota::delete/$1');
	$subroutes->add('apply_status/(:any)', 'PerpanjanganAnggota::apply_status/$1');
	$subroutes->add('do_init', 'PerpanjanganAnggota::do_init');
	$subroutes->add('do_upload', 'PerpanjanganAnggota::do_upload');
	$subroutes->add('do_delete', 'PerpanjanganAnggota::do_delete');
	$subroutes->add('flip', 'PerpanjanganAnggota::flip');
});

$routes->group('api/perpanjangan-anggota', ['namespace' => 'PerpanjanganAnggota\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'PerpanjanganAnggota::detail/$1');
	$subroutes->add('create', 'PerpanjanganAnggota::create');
	$subroutes->add('edit/(:any)', 'PerpanjanganAnggota::edit/$1');
	$subroutes->add('delete/(:any)', 'PerpanjanganAnggota::delete/$1');
});