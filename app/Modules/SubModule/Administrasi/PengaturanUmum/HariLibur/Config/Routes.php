<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-hari-libur', ['namespace' => 'HariLibur\Controllers'], function ($subroutes) {
	$subroutes->add('', 'HariLibur::index');
	$subroutes->add('index', 'HariLibur::index');
	$subroutes->add('detail/(:any)', 'HariLibur::detail/$1');
	$subroutes->add('create', 'HariLibur::create');
	$subroutes->add('edit/(:any)', 'HariLibur::edit/$1');
	$subroutes->add('delete/(:any)', 'HariLibur::delete/$1');
	$subroutes->add('apply_status/(:any)', 'HariLibur::apply_status/$1');
	$subroutes->add('do_init', 'HariLibur::do_init');
	$subroutes->add('do_upload', 'HariLibur::do_upload');
	$subroutes->add('do_delete', 'HariLibur::do_delete');
	$subroutes->add('flip', 'HariLibur::flip');
});

$routes->group('api/hari-libur', ['namespace' => 'HariLibur\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'HariLibur::index');
	$subroutes->add('detail/(:any)', 'HariLibur::detail/$1');
	$subroutes->add('create', 'HariLibur::create');
	$subroutes->post('createliburpanjang', 'HariLibur::createliburpanjang');
	$subroutes->add('edit/(:any)', 'HariLibur::edit/$1');
	$subroutes->add('delete/(:any)', 'HariLibur::delete/$1');

	//custom
	$subroutes->add('datatable', 'HariLibur::datatable');
	$subroutes->add('datatable/(:any)', 'HariLibur::datatable/$1');
});