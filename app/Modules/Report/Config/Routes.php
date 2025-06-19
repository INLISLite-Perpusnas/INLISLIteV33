<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('home', ['namespace' => 'Report\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Report::index');
	$subroutes->add('index', 'Report::index');
	$subroutes->add('visitor', 'Report::visitor');
	$subroutes->add('visitor_export', 'Report::visitor_export');
	$subroutes->add('member', 'Report::member');
	$subroutes->add('member_export', 'Report::member_export');
});

$routes->group('api/report', ['namespace' => 'Report\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Report::index');
	$subroutes->add('index', 'Report::index');

	//custom
	$subroutes->add('visitor_datatable', 'Report::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'Report::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'Report::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'Report::member_datatable/$1');
});
