<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-peminjaman', ['namespace' => 'Peminjaman\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Peminjaman::index');
	$subroutes->add('index', 'Peminjaman::index');
	$subroutes->add('create_loan', 'Peminjaman::create_loan');
	$subroutes->add('detail/(:any)', 'Peminjaman::detail/$1');
	$subroutes->add('create', 'Peminjaman::create');
	$subroutes->add('edit/(:any)', 'Peminjaman::edit/$1');
	$subroutes->add('delete/(:any)', 'Peminjaman::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Peminjaman::apply_status/$1');
	$subroutes->add('do_init', 'Peminjaman::do_init');
	$subroutes->add('do_upload', 'Peminjaman::do_upload');
	$subroutes->add('do_delete', 'Peminjaman::do_delete');
	$subroutes->add('flip', 'Peminjaman::flip');
	$subroutes->post('loan_digital_store', 'Peminjaman::loan_digital_store');

	//custom
	$subroutes->add('cart_insert/(:any)', 'Peminjaman::cart_insert/$1');
	$subroutes->add('cart_remove/(:any)', 'Peminjaman::cart_remove/$1');
	$subroutes->add('cart_destroy', 'Peminjaman::cart_destroy');
	$subroutes->add('mandiri', 'Peminjaman::mandiri');
});

$routes->group('api/sirkulasi-peminjaman', ['namespace' => 'Peminjaman\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Peminjaman::detail/$1');
	$subroutes->add('create', 'Peminjaman::create');
	$subroutes->add('loan_history', 'Peminjaman::loan_history');
	$subroutes->post('CreateLoan', 'Peminjaman::CreateLoan');
	$subroutes->add('edit/(:any)', 'Peminjaman::edit/$1');
	$subroutes->add('delete/(:any)', 'Peminjaman::delete/$1');

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
