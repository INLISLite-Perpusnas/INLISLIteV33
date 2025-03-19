<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-penyedia-katalog', ['namespace' => 'PenyediaKatalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PenyediaKatalog::index');
	$subroutes->add('index', 'PenyediaKatalog::index');
	$subroutes->add('create', 'PenyediaKatalog::create');
	$subroutes->add('edit/(:any)', 'PenyediaKatalog::edit/$1');
	$subroutes->add('delete/(:any)', 'PenyediaKatalog::delete/$1');
});

$routes->group('api/master-penyedia-katalog', ['namespace' => 'PenyediaKatalog\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'PenyediaKatalog::datatable');
	$subroutes->add('datatable/(:any)', 'PenyediaKatalog::datatable/$1');
	$subroutes->add('switch/(:any)', 'PenyediaKatalog::switch/$1');
	$subroutes->add('item_data_delete/(:any)', 'PenyediaKatalog::item_data_delete/$1');
});
