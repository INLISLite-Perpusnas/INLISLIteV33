<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('flip', ['namespace' => 'Flip\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Flip::index');
});