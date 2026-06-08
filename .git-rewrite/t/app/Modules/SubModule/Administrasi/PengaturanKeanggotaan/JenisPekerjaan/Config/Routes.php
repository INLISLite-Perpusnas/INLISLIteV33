<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-pekerjaan', ['namespace' => 'JenisPekerjaan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisPekerjaan::index');
	$subroutes->add('index', 'JenisPekerjaan::index');
	$subroutes->add('detail/(:any)', 'JenisPekerjaan::detail/$1');
	$subroutes->add('create', 'JenisPekerjaan::create');
	$subroutes->add('edit/(:any)', 'JenisPekerjaan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPekerjaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisPekerjaan::apply_status/$1');
	$subroutes->add('do_init', 'JenisPekerjaan::do_init');
	$subroutes->add('do_upload', 'JenisPekerjaan::do_upload');
	$subroutes->add('do_delete', 'JenisPekerjaan::do_delete');
	$subroutes->add('flip', 'JenisPekerjaan::flip');
});

$routes->group('api/jenis-pekerjaan', ['namespace' => 'JenisPekerjaan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'JenisPekerjaan::index');
	$subroutes->add('detail/(:any)', 'JenisPekerjaan::detail/$1');
	$subroutes->add('create', 'JenisPekerjaan::create');
	$subroutes->add('edit/(:any)', 'JenisPekerjaan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPekerjaan::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisPekerjaan::datatable');
	$subroutes->add('datatable/(:any)', 'JenisPekerjaan::datatable/$1');
});