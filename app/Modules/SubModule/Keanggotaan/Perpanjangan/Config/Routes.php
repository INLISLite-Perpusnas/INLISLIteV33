<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('perpanjangan', ['namespace' => 'Perpanjangan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Perpanjangan::index');
	$subroutes->add('index', 'Perpanjangan::index');
	$subroutes->add('detail/(:any)', 'Perpanjangan::detail/$1');
	$subroutes->add('create', 'Perpanjangan::create');
	$subroutes->add('edit/(:any)', 'Perpanjangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Perpanjangan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Perpanjangan::apply_status/$1');
	$subroutes->add('do_init', 'Perpanjangan::do_init');
	$subroutes->add('do_upload', 'Perpanjangan::do_upload');
	$subroutes->add('do_delete', 'Perpanjangan::do_delete');
	$subroutes->add('flip', 'Perpanjangan::flip');
});

$routes->group('api/perpanjangan', ['namespace' => 'Perpanjangan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Perpanjangan::detail/$1');
	$subroutes->add('create', 'Perpanjangan::create');
	$subroutes->add('edit/(:any)', 'Perpanjangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Perpanjangan::delete/$1');
});