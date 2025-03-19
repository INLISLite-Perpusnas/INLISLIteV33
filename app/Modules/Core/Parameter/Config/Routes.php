<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('parameter', ['namespace' => 'Parameter\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Parameter::index');
	$subroutes->add('index', 'Parameter::index');
	$subroutes->add('detail/(:any)', 'Parameter::detail/$1');
	$subroutes->add('detail/(:any)/(:any)', 'Parameter::detail/$1/$1');
	$subroutes->add('create', 'Parameter::create');
	$subroutes->add('edit/(:any)', 'Parameter::edit/$1');
	$subroutes->add('delete/(:any)', 'Parameter::delete/$1');
	$subroutes->add('set_access/(:any)', 'Parameter::set_access/$1');
});

$routes->group('api/parameter', ['namespace' => 'Parameter\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'Parameter::datatable');
	$subroutes->add('datatable/(:any)', 'Parameter::datatable/$1');
	$subroutes->add('detail/(:any)', 'Parameter::detail/$1');
	$subroutes->add('show/(:any)', 'Parameter::show/$1');
	$subroutes->add('edit/(:any)', 'Parameter::edit/$1');
	$subroutes->add('create', 'Parameter::create');
	$subroutes->add('delete/(:any)', 'Parameter::delete/$1');
});
