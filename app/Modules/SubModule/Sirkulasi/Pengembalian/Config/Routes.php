<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('sirkulasi-pengembalian', ['namespace' => 'Pengembalian\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Pengembalian::index');
	$subroutes->add('index', 'Pengembalian::index');
	$subroutes->get('create', 'Pengembalian::create');
	$subroutes->add('apply_status/(:any)', 'Pengembalian::apply_status/$1');
    $subroutes->add('process-return', 'Pengembalian::processReturn');
	$subroutes->add('check-book', 'Pengembalian::checkBook');
	$subroutes->get('get-jenis-pelanggaran', 'Pengembalian::getJenisPelanggaran');
	$subroutes->get('get-jenis-denda', 'Pengembalian::getJenisDenda');
	$subroutes->add('getReturnHistory', 'Pengembalian::getReturnHistory');
	$subroutes->get('success', 'Pengembalian::success');
	$subroutes->post('send-struk', 'Pengembalian::sendStruk');
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
