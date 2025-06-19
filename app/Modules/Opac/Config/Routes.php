<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('opac', ['namespace' => 'Opac\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Opac::index');
	$subroutes->add('index', 'Opac::index');
	$subroutes->add('statistics', 'Opac::statistics');
	$subroutes->add('browse', 'Opac::browse');
	$subroutes->add('detail/(:any)', 'Opac::detail/$1');
	$subroutes->add('visitor_export', 'Opac::visitor_export');
	$subroutes->add('member', 'Opac::member');
	$subroutes->add('member_export', 'Opac::member_export');
});

$routes->group('api/Opac', ['namespace' => 'Opac\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Opac::index');
	$subroutes->add('index', 'Opac::index');

	//custom
	$subroutes->add('visitor_datatable', 'Opac::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'Opac::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'Opac::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'Opac::member_datatable/$1');
});
