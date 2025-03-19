<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-bahan-pustaka', ['namespace' => 'JenisBahanPustaka\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisBahanPustaka::index');
	$subroutes->add('index', 'JenisBahanPustaka::index');
	$subroutes->add('create', 'JenisBahanPustaka::create');
	$subroutes->add('edit/(:any)', 'JenisBahanPustaka::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisBahanPustaka::delete/$1');
});

$routes->group('api/master-jenis-bahan-pustaka', ['namespace' => 'JenisBahanPustaka\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'JenisBahanPustaka::datatable');
	$subroutes->add('datatable/(:any)', 'JenisBahanPustaka::datatable/$1');
	$subroutes->add('switch/(:any)', 'JenisBahanPustaka::switch/$1');
	$subroutes->add('wsf_delete/(:any)', 'JenisBahanPustaka::wsf_delete/$1');
});
