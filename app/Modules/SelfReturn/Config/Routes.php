<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('pengembalian-mandiri', ['namespace' => 'SelfReturn\Controllers'], function ($subroutes) {
	/*** Route Update for SelfReturn ***/
	$subroutes->add('', 'SelfReturn::index');
	$subroutes->add('index', 'SelfReturn::index');

	//custom
	$subroutes->add('do_return', 'SelfReturn::do_return');
	$subroutes->add('do_return/(:any)', 'SelfReturn::do_return/$1');
});

$routes->group('api/pengembalian-mandiri', ['namespace' => 'SelfReturn\Controllers\Api'], function ($subroutes) {
	/*** Route Update for SelfReturn ***/

	//custom
	$subroutes->add('datatable', 'SelfReturn::datatable');
	$subroutes->add('datatable/(:any)', 'SelfReturn::datatable/$1');
	$subroutes->add('loan_datatable', 'SelfReturn::loan_datatable');
});
