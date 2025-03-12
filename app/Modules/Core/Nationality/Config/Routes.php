<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('api/nationality', ['namespace' => 'Nationality\Controllers\Api'], function ($subroutes) {$subroutes->get('countries', 'Nationality::get_countries');
	$subroutes->get('city/(:any)', 'Nationality::get_cities/$1');
});
