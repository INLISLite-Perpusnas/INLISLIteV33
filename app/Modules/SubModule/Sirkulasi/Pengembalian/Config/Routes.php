<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-pengembalian', ['namespace' => 'Pengembalian\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Pengembalian::index');
	$subroutes->add('index', 'Pengembalian::index');
	$subroutes->add('detail/(:any)', 'Pengembalian::detail/$1');
	$subroutes->add('create', 'Pengembalian::create');
	$subroutes->add('edit/(:any)', 'Pengembalian::edit/$1');
	$subroutes->add('delete/(:any)', 'Pengembalian::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Pengembalian::apply_status/$1');
	$subroutes->add('do_init', 'Pengembalian::do_init');
	$subroutes->add('do_upload', 'Pengembalian::do_upload');
	$subroutes->add('do_delete', 'Pengembalian::do_delete');
	$subroutes->add('flip', 'Pengembalian::flip');

	//custom
	$subroutes->add('do_return', 'Pengembalian::do_return');
	$subroutes->add('do_return/(:any)', 'Pengembalian::do_return/$1');
	$subroutes->add('cart_insert', 'Pengembalian::cart_insert/0');
	$subroutes->add('cart_remove/(:any)', 'Pengembalian::cart_remove/$1');
	$subroutes->add('cart_destroy', 'Pengembalian::cart_destroy');
});

$routes->group('api/sirkulasi-pengembalian', ['namespace' => 'Pengembalian\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Pengembalian::detail/$1');
	$subroutes->add('edit/(:any)', 'Pengembalian::edit/$1');
	$subroutes->add('create', 'Pengembalian::create');
	$subroutes->add('save_violation', 'Pengembalian::save_violation');
	$subroutes->add('delete/(:any)', 'Pengembalian::delete/$1');

	//custom
	$subroutes->add('datatable', 'Pengembalian::datatable');
	$subroutes->add('datatable/(:any)', 'Pengembalian::datatable/$1');
	$subroutes->add('loan_datatable', 'Pengembalian::loan_datatable');
	$subroutes->add('switch/(:any)', 'Pengembalian::switch/$1');
});
