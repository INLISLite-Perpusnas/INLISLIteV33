<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-referensi', ['namespace' => 'Referensi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Referensi::index');
	$subroutes->add('index', 'Referensi::index');
	$subroutes->add('create', 'Referensi::create');
	$subroutes->add('edit/(:any)', 'Referensi::edit/$1');
	$subroutes->add('delete/(:any)', 'Referensi::delete/$1');
});

$routes->group('api/master-referensi', ['namespace' => 'Referensi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'Referensi::datatable');
	$subroutes->add('datatable/(:any)', 'Referensi::datatable/$1');
	$subroutes->add('switch/(:any)', 'Referensi::switch/$1');
	$subroutes->add('item_data_delete/(:any)', 'Referensi::item_data_delete/$1');
});
