<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('reference', ['namespace' => 'Reference\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Reference::index');
	$subroutes->add('reference', 'Reference::index');
	$subroutes->add('index', 'Reference::index');
	$subroutes->add('detail/(:any)', 'Reference::detail/$1');
	$subroutes->add('edit/(:any)', 'Reference::edit/$1');
	$subroutes->add('create', 'Reference::create');
	$subroutes->add('delete/(:any)', 'Reference::delete/$1');
	$subroutes->add('delete_category/(:any)', 'Reference::delete_category/$1');
	$subroutes->add('enable/(:any)', 'Reference::enable/$1');
	$subroutes->add('disable/(:any)', 'Reference::disable/$1');
	$subroutes->add('do_init', 'Reference::do_init');
	$subroutes->add('do_upload', 'Reference::do_upload');
	$subroutes->add('do_delete', 'Reference::do_delete');
	$subroutes->add('flip', 'Reference::flip');
});

$routes->group('api/reference', ['namespace' => 'Reference\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Reference::detail/$1');
	$subroutes->add('edit/(:any)', 'Reference::edit/$1');
	$subroutes->add('create', 'Reference::create');
	$subroutes->add('delete/(:any)', 'Reference::delete/$1');
	$subroutes->add('list/(:any)', 'Reference::list/$1');
});