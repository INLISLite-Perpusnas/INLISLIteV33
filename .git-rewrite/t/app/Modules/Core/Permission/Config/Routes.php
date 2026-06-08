<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('permission', ['namespace' => 'Permission\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Permission::index');
	$subroutes->add('index', 'Permission::index');
	$subroutes->add('create', 'Permission::create');
	$subroutes->add('detail/(:any)', 'Permission::detail/$1');
	$subroutes->add('edit/(:any)', 'Permission::edit/$1');
	$subroutes->add('delete/(:any)', 'Permission::delete/$1');
});

$routes->group('api/permission', ['namespace' => 'Permission\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Permission::detail/$1');
	$subroutes->add('edit/(:any)', 'Permission::edit/$1');
	$subroutes->add('create', 'Permission::create');
	$subroutes->add('delete/(:any)', 'Permission::delete/$1');
});
