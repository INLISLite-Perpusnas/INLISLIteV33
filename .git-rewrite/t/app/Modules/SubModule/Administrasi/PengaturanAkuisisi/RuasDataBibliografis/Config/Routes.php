<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('ruas-data-bibliografis', ['namespace' => 'RuasDataBibliografis\Controllers'], function ($subroutes) {
	$subroutes->add('', 'RuasDataBibliografis::index');
	$subroutes->add('index', 'RuasDataBibliografis::index');
	
	$subroutes->add('updateActive', 'RuasDataBibliografis::updateActive');
	
});


