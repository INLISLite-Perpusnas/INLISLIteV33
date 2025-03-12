<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-identitas', ['namespace' => 'JenisIdentitas\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisIdentitas::index');
	$subroutes->add('index', 'JenisIdentitas::index');
	$subroutes->add('detail/(:any)', 'JenisIdentitas::detail/$1');
	$subroutes->add('create', 'JenisIdentitas::create');
	$subroutes->add('edit/(:any)', 'JenisIdentitas::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisIdentitas::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisIdentitas::apply_status/$1');
	$subroutes->add('do_init', 'JenisIdentitas::do_init');
	$subroutes->add('do_upload', 'JenisIdentitas::do_upload');
	$subroutes->add('do_delete', 'JenisIdentitas::do_delete');
	$subroutes->add('flip', 'JenisIdentitas::flip');
});

$routes->group('api/jenis-identitas', ['namespace' => 'JenisIdentitas\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisIdentitas::detail/$1');
	$subroutes->add('create', 'JenisIdentitas::create');
	$subroutes->add('edit/(:any)', 'JenisIdentitas::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisIdentitas::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisIdentitas::datatable');
	$subroutes->add('datatable/(:any)', 'JenisIdentitas::datatable/$1');
});