<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-peminjaman', ['namespace' => 'Peminjaman\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Peminjaman::index');
	$subroutes->add('index', 'Peminjaman::index');
	$subroutes->add('create', 'Peminjaman::create');
	$subroutes->add('apply_status/(:any)', 'Peminjaman::apply_status/$1');
	$subroutes->add('process-loan', 'Peminjaman::processLoan');
	$subroutes->add('remove-book', 'Peminjaman::removeBook');
	$subroutes->add('success', 'Peminjaman::success');
	//custom
	$subroutes->add('mandiri', 'Peminjaman::mandiri');
	$subroutes->post('send-notification/(:num)',  'Peminjaman::sendNotification/$1');
	$subroutes->post('send-all-notification',     'Peminjaman::sendAllNotification');
	$subroutes->get('overdue-summary',            'Peminjaman::overdueSummary');
});

$routes->group('api/sirkulasi-peminjaman', ['namespace' => 'Peminjaman\Controllers\Api'], function ($subroutes) {
	
	$subroutes->add('loan_history', 'Peminjaman::loan_history');
 
 
	//custom
	$subroutes->add('datatable', 'Peminjaman::datatable');
	$subroutes->add('datatable/(:any)', 'Peminjaman::datatable/$1');
	$subroutes->add('loan_datatable', 'Peminjaman::loan_datatable');
	$subroutes->add('loan_datatable/(:any)', 'Peminjaman::loan_datatable/$1');
	$subroutes->add('loan_datatable_simple/(:any)', 'Peminjaman::loan_datatable_simple/$1');
	$subroutes->add('koleksi', 'Peminjaman::koleksi');
	$subroutes->add('koleksi/(:any)', 'Peminjaman::koleksi/$1');
	$subroutes->add('switch/(:any)', 'Peminjaman::switch/$1');
});
