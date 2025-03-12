<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sumbangan', ['namespace' => 'Sumbangan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Sumbangan::index');
	$subroutes->add('index', 'Sumbangan::index');
	$subroutes->add('detail/(:any)', 'Sumbangan::detail/$1');
	$subroutes->add('create', 'Sumbangan::create');
	$subroutes->add('edit/(:any)', 'Sumbangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Sumbangan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Sumbangan::apply_status/$1');
	$subroutes->add('do_init', 'Sumbangan::do_init');
	$subroutes->add('do_upload', 'Sumbangan::do_upload');
	$subroutes->add('do_delete', 'Sumbangan::do_delete');
	$subroutes->add('flip', 'Sumbangan::flip');
});

$routes->group('api/sumbangan', ['namespace' => 'Sumbangan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Sumbangan::detail/$1');
	$subroutes->add('create', 'Sumbangan::create');
	$subroutes->add('edit/(:any)', 'Sumbangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Sumbangan::delete/$1');
});