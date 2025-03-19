<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('group', ['namespace' => 'Group\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Group::index');
	$subroutes->add('index', 'Group::index');
	$subroutes->add('detail/(:any)', 'Group::detail/$1');
	$subroutes->add('create', 'Group::create');
	$subroutes->add('edit/(:any)', 'Group::edit/$1');
	$subroutes->add('delete/(:any)', 'Group::delete/$1');
	$subroutes->add('enable/(:any)', 'Group::enable/$1');
	$subroutes->add('disable/(:any)', 'Group::disable/$1');
	$subroutes->add('permissions', 'Group::permissions');
	$subroutes->add('permissions/(:any)', 'Group::permissions/$1');
});

$routes->group('api/group', ['namespace' => 'Group\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Group::detail/$1');
	$subroutes->add('edit/(:any)', 'Group::edit/$1');
	$subroutes->add('create', 'Group::create');
	$subroutes->add('delete/(:any)', 'Group::delete/$1');
});

