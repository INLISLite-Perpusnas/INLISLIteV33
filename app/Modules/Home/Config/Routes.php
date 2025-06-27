<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
// Update your existing routes to include recommendations

$routes->group('home', ['namespace' => 'Home\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');

});

$routes->group('api/home', ['namespace' => 'Home\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');

	
});