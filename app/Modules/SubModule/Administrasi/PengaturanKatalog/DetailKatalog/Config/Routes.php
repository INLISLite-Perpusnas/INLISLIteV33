<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-detail-katalog', ['namespace' => 'DetailKatalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'DetailKatalog::index');
	$subroutes->add('index', 'DetailKatalog::index');
	$subroutes->add('delete/(:any)', 'DetailKatalog::delete/$1');
});

$routes->group('api/master-detail-katalog', ['namespace' => 'DetailKatalog\Controllers\Api'], function ($subroutes) {
	$subroutes->add('create', 'DetailKatalog::create');
	$subroutes->add('delete/(:any)', 'DetailKatalog::delete/$1');
	$subroutes->add('datatable', 'DetailKatalog::datatable');
});
