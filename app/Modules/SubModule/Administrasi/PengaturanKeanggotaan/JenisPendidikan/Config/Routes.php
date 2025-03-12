<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-pendidikan', ['namespace' => 'JenisPendidikan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisPendidikan::index');
	$subroutes->add('index', 'JenisPendidikan::index');
	$subroutes->add('detail/(:any)', 'JenisPendidikan::detail/$1');
	$subroutes->add('create', 'JenisPendidikan::create');
	$subroutes->add('edit/(:any)', 'JenisPendidikan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPendidikan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisPendidikan::apply_status/$1');
	$subroutes->add('do_init', 'JenisPendidikan::do_init');
	$subroutes->add('do_upload', 'JenisPendidikan::do_upload');
	$subroutes->add('do_delete', 'JenisPendidikan::do_delete');
	$subroutes->add('flip', 'JenisPendidikan::flip');
});

$routes->group('api/jenis-pendidikan', ['namespace' => 'JenisPendidikan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'JenisPendidikan::index');
	$subroutes->add('detail/(:any)', 'JenisPendidikan::detail/$1');
	$subroutes->add('create', 'JenisPendidikan::create');
	$subroutes->add('edit/(:any)', 'JenisPendidikan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPendidikan::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisPendidikan::datatable');
	$subroutes->add('datatable/(:any)', 'JenisPendidikan::datatable/$1');
});