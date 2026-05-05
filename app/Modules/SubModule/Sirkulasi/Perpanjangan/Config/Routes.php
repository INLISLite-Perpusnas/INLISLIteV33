<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-perpanjangan', ['namespace' => 'Perpanjangan\Controllers'], function ($subroutes) {
    $subroutes->add('', 'Perpanjangan::index');
    $subroutes->add('index', 'Perpanjangan::index');

    $subroutes->get('create',             'Perpanjangan::create');
    $subroutes->post('check-book',        'Perpanjangan::checkBook');
    $subroutes->post('process-extend',    'Perpanjangan::processExtend');
    $subroutes->get('get-extend-history', 'Perpanjangan::getExtendHistory');
});

$routes->group('api/sirkulasi-perpanjangan', ['namespace' => 'Perpanjangan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Perpanjangan::detail/$1');
	$subroutes->add('create', 'Perpanjangan::create');
	$subroutes->add('edit/(:any)', 'Perpanjangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Perpanjangan::delete/$1');

	//custom
	$subroutes->add('datatable', 'Perpanjangan::datatable');
	$subroutes->add('datatable/(:any)', 'Perpanjangan::datatable/$1');
	$subroutes->add('loan_datatable', 'Perpanjangan::loan_datatable');
	$subroutes->add('switch/(:any)', 'Perpanjangan::switch/$1');
});
