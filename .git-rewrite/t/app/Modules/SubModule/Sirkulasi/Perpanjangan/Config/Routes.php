<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-perpanjangan', ['namespace' => 'Perpanjangan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Perpanjangan::index');
	$subroutes->add('index', 'Perpanjangan::index');
	$subroutes->add('detail/(:any)', 'Perpanjangan::detail/$1');
	$subroutes->add('create', 'Perpanjangan::create');
	$subroutes->add('edit/(:any)', 'Perpanjangan::edit/$1');
	$subroutes->add('delete/(:any)', 'Perpanjangan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Perpanjangan::apply_status/$1');
	$subroutes->add('do_init', 'Perpanjangan::do_init');
	$subroutes->add('do_upload', 'Perpanjangan::do_upload');
	$subroutes->add('do_delete', 'Perpanjangan::do_delete');
	$subroutes->add('flip', 'Perpanjangan::flip');

	//custom
	$subroutes->add('do_extend', 'Perpanjangan::do_extend');
	$subroutes->add('do_extend/(:any)', 'Perpanjangan::do_extend/$1');
	$subroutes->add('cart_insert', 'Perpanjangan::cart_insert/0');
	$subroutes->add('cart_remove/(:any)', 'Perpanjangan::cart_remove/$1');
	$subroutes->add('cart_destroy', 'Perpanjangan::cart_destroy');
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
