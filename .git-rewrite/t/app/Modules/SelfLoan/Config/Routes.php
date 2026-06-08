<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('peminjaman-mandiri', ['namespace' => 'SelfLoan\Controllers'], function ($subroutes) {
	$subroutes->add('/', 'SelfLoan::index');
    $subroutes->add('remove-book', 'SelfLoan::removeBook');
	$subroutes->add('process-loan', 'SelfLoan::processLoan');
    $subroutes->add('success', 'SelfLoan::success');

	//custom
	$subroutes->add('cart_insert/(:any)', 'SelfLoan::cart_insert/$1');
	$subroutes->add('cart_remove/(:any)', 'SelfLoan::cart_remove/$1');
	$subroutes->add('cart_destroy', 'SelfLoan::cart_destroy');
	$subroutes->add('mandiri', 'SelfLoan::mandiri');
});

$routes->group('api/sirkulasi-SelfLoan', ['namespace' => 'SelfLoan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'SelfLoan::detail/$1');
	$subroutes->add('create', 'SelfLoan::create');
	$subroutes->add('loan_history', 'SelfLoan::loan_history');
	$subroutes->post('CreateLoan', 'SelfLoan::CreateLoan');
	$subroutes->add('edit/(:any)', 'SelfLoan::edit/$1');
	$subroutes->add('delete/(:any)', 'SelfLoan::delete/$1');

	//custom
	$subroutes->add('datatable', 'SelfLoan::datatable');
	$subroutes->add('datatable/(:any)', 'SelfLoan::datatable/$1');
	$subroutes->add('loan_datatable', 'SelfLoan::loan_datatable');
	$subroutes->add('loan_datatable/(:any)', 'SelfLoan::loan_datatable/$1');
	$subroutes->add('loan_datatable_simple/(:any)', 'SelfLoan::loan_datatable_simple/$1');
	$subroutes->add('koleksi', 'SelfLoan::koleksi');
	$subroutes->add('koleksi/(:any)', 'SelfLoan::koleksi/$1');
	$subroutes->add('switch/(:any)', 'SelfLoan::switch/$1');
});
