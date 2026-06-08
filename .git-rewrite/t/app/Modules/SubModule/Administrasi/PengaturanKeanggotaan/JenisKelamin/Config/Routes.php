<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-kelamin', ['namespace' => 'JenisKelamin\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisKelamin::index');
	$subroutes->add('index', 'JenisKelamin::index');
	$subroutes->add('detail/(:any)', 'JenisKelamin::detail/$1');
	$subroutes->add('create', 'JenisKelamin::create');
	$subroutes->add('edit/(:any)', 'JenisKelamin::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisKelamin::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisKelamin::apply_status/$1');
	$subroutes->add('do_init', 'JenisKelamin::do_init');
	$subroutes->add('do_upload', 'JenisKelamin::do_upload');
	$subroutes->add('do_delete', 'JenisKelamin::do_delete');
	$subroutes->add('flip', 'JenisKelamin::flip');
});

$routes->group('api/jenis-kelamin', ['namespace' => 'JenisKelamin\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisKelamin::detail/$1');
	$subroutes->add('create', 'JenisKelamin::create');
	$subroutes->add('edit/(:any)', 'JenisKelamin::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisKelamin::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisKelamin::datatable');
	$subroutes->add('datatable/(:any)', 'JenisKelamin::datatable/$1');
});