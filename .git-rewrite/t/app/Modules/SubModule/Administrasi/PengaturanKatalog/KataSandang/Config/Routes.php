<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('master-kata-sandang', ['namespace' => 'KataSandang\Controllers'], function ($subroutes) {
	$subroutes->add('', 'KataSandang::index');
	$subroutes->add('index', 'KataSandang::index');
	$subroutes->add('detail/(:any)', 'KataSandang::detail/$1');
	$subroutes->add('create', 'KataSandang::create');
	$subroutes->add('edit/(:any)', 'KataSandang::edit/$1');
	$subroutes->add('delete/(:any)', 'KataSandang::delete/$1');
});

$routes->group('api/master-kata-sandang', ['namespace' => 'KataSandang\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'KataSandang::datatable');
	$subroutes->add('datatable/(:any)', 'KataSandang::datatable/$1');
	$subroutes->add('detail/(:any)', 'KataSandang::detail/$1');
	$subroutes->add('show/(:any)', 'KataSandang::show/$1');
	$subroutes->add('edit/(:any)', 'KataSandang::edit/$1');
	$subroutes->add('create', 'KataSandang::create');
	$subroutes->add('delete/(:any)', 'KataSandang::delete/$1');
});
