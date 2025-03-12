<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('widget', ['namespace' => 'Widget\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Widget::index');
	$subroutes->add('convert/(:any)', 'Widget::convert/$1');
});