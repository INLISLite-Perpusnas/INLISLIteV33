<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('pengembalian-mandiri', ['namespace' => 'SelfReturn\Controllers'], function ($subroutes) {
    // Main routes - hanya perlu scan barcode
	$subroutes->add('/', 'SelfReturn::index');
    $subroutes->add('check-book', 'SelfReturn::checkBook');
    $subroutes->add('process-return', 'SelfReturn::processReturn');
    $subroutes->add('history', 'SelfReturn::getReturnHistory');
});

$routes->group('api/pengembalian-mandiri', ['namespace' => 'SelfReturn\Controllers\Api'], function ($subroutes) {
	/*** Route Update for SelfReturn ***/

	//custom
	$subroutes->add('datatable', 'SelfReturn::datatable');
	$subroutes->add('datatable/(:any)', 'SelfReturn::datatable/$1');
	$subroutes->add('loan_datatable', 'SelfReturn::loan_datatable');
});
