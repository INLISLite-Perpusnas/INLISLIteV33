<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('master-format-kartu', ['namespace' => 'FormatKartu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'FormatKartu::index');
	$subroutes->add('index', 'FormatKartu::index');
	$subroutes->add('delete/(:any)', 'FormatKartu::delete/$1');
});

$routes->group('api/master-format-kartu', ['namespace' => 'FormatKartu\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'FormatKartu::datatable');
	$subroutes->add('datatable/(:any)', 'FormatKartu::datatable/$1');
	$subroutes->add('detail/(:any)', 'FormatKartu::detail/$1');
	$subroutes->add('show/(:any)', 'FormatKartu::show/$1');
	$subroutes->add('edit/(:any)', 'FormatKartu::edit/$1');
	$subroutes->add('create', 'FormatKartu::create');
	$subroutes->add('delete/(:any)', 'FormatKartu::delete/$1');
});
