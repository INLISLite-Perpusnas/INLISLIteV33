<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-bahan', ['namespace' => 'JenisBahan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisBahan::index');
	$subroutes->add('index', 'JenisBahan::index');
	$subroutes->add('detail/(:any)', 'JenisBahan::detail/$1');
	$subroutes->add('create', 'JenisBahan::create');
	$subroutes->add('edit/(:any)', 'JenisBahan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisBahan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisBahan::apply_status/$1');
	$subroutes->add('do_init', 'JenisBahan::do_init');
	$subroutes->add('do_upload', 'JenisBahan::do_upload');
	$subroutes->add('do_delete', 'JenisBahan::do_delete');
	$subroutes->add('flip', 'JenisBahan::flip');
});

$routes->group('api/master-jenis-bahan', ['namespace' => 'JenisBahan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisBahan::detail/$1');
	$subroutes->add('create', 'JenisBahan::create');
	$subroutes->add('edit/(:any)', 'JenisBahan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisBahan::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisBahan::datatable');
	$subroutes->add('datatable/(:any)', 'JenisBahan::datatable/$1');
});
