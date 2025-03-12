<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-denda', ['namespace' => 'JenisDenda\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisDenda::index');
	$subroutes->add('index', 'JenisDenda::index');
	$subroutes->add('detail/(:any)', 'JenisDenda::detail/$1');
	$subroutes->add('create', 'JenisDenda::create');
	$subroutes->add('edit/(:any)', 'JenisDenda::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisDenda::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisDenda::apply_status/$1');
	$subroutes->add('do_init', 'JenisDenda::do_init');
	$subroutes->add('do_upload', 'JenisDenda::do_upload');
	$subroutes->add('do_delete', 'JenisDenda::do_delete');
	$subroutes->add('flip', 'JenisDenda::flip');
});

$routes->group('api/jenis-denda', ['namespace' => 'JenisDenda\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisDenda::detail/$1');
	$subroutes->add('create', 'JenisDenda::create');
	$subroutes->add('edit/(:any)', 'JenisDenda::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisDenda::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisDenda::datatable');
	$subroutes->add('datatable/(:any)', 'JenisDenda::datatable/$1');
});