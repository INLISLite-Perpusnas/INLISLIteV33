<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-form-entri', ['namespace' => 'FormEntri\Controllers'], function ($subroutes) {
	$subroutes->add('', 'FormEntri::index');
	$subroutes->add('index', 'FormEntri::index');
});
