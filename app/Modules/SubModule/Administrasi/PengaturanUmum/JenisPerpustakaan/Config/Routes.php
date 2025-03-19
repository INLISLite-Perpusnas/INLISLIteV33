<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-perpustakaan', ['namespace' => 'JenisPerpustakaan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisPerpustakaan::index');
	$subroutes->add('index', 'JenisPerpustakaan::index');
	$subroutes->add('detail/(:any)', 'JenisPerpustakaan::detail/$1');
	$subroutes->add('create', 'JenisPerpustakaan::create');
	$subroutes->add('edit/(:any)', 'JenisPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPerpustakaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisPerpustakaan::apply_status/$1');
});

$routes->group('api-jenis-perpustakaan', ['namespace' => 'JenisPerpustakaan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisPerpustakaan::detail/$1');
	$subroutes->add('create', 'JenisPerpustakaan::create');
	$subroutes->add('edit/(:any)', 'JenisPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisPerpustakaan::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisPerpustakaan::datatable');
	$subroutes->add('datatable/(:any)', 'JenisPerpustakaan::datatable/$1');
	$subroutes->add('datatable_form/(:any)', 'JenisPerpustakaan::datatable_form/$1');
	$subroutes->add('form/(:any)', 'JenisPerpustakaan::form/$1');
	$subroutes->add('update_field/(:any)', 'JenisPerpustakaan::update_field/$1');
});
