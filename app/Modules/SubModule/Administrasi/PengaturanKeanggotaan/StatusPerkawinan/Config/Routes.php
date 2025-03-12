<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-status-perkawinan', ['namespace' => 'StatusPerkawinan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'StatusPerkawinan::index');
	$subroutes->add('index', 'StatusPerkawinan::index');
	$subroutes->add('detail/(:any)', 'StatusPerkawinan::detail/$1');
	$subroutes->add('create', 'StatusPerkawinan::create');
	$subroutes->add('edit/(:any)', 'StatusPerkawinan::edit/$1');
	$subroutes->add('delete/(:any)', 'StatusPerkawinan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'StatusPerkawinan::apply_status/$1');
	$subroutes->add('do_init', 'StatusPerkawinan::do_init');
	$subroutes->add('do_upload', 'StatusPerkawinan::do_upload');
	$subroutes->add('do_delete', 'StatusPerkawinan::do_delete');
	$subroutes->add('flip', 'StatusPerkawinan::flip');
});

$routes->group('api-status-perkawinan', ['namespace' => 'StatusPerkawinan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'StatusPerkawinan::detail/$1');
	$subroutes->add('create', 'StatusPerkawinan::create');
	$subroutes->add('edit/(:any)', 'StatusPerkawinan::edit/$1');
	$subroutes->add('delete/(:any)', 'StatusPerkawinan::delete/$1');

	//custom
	$subroutes->add('datatable', 'StatusPerkawinan::datatable');
	$subroutes->add('datatable/(:any)', 'StatusPerkawinan::datatable/$1');
});