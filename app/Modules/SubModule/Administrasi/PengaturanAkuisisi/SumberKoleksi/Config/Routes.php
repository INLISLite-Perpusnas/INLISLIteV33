<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-sumber-koleksi', ['namespace' => 'SumberKoleksi\Controllers'], function ($subroutes) {
	/*** Route Update for SumberKoleksi ***/
	$subroutes->add('', 'SumberKoleksi::index');
	$subroutes->add('index', 'SumberKoleksi::index');
	$subroutes->add('detail/(:any)', 'SumberKoleksi::detail/$1');
	$subroutes->add('create', 'SumberKoleksi::create');
	$subroutes->add('edit/(:any)', 'SumberKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'SumberKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'SumberKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'SumberKoleksi::do_init');
	$subroutes->add('do_upload', 'SumberKoleksi::do_upload');
	$subroutes->add('do_delete', 'SumberKoleksi::do_delete');
	$subroutes->add('flip', 'SumberKoleksi::flip');
});

$routes->group('api/master-sumber-koleksi', ['namespace' => 'SumberKoleksi\Controllers\Api'], function ($subroutes) {
	/*** Route Update for SumberKoleksi ***/
	$subroutes->add('detail/(:any)', 'SumberKoleksi::detail/$1');
	$subroutes->add('create', 'SumberKoleksi::create');
	$subroutes->add('edit/(:any)', 'SumberKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'SumberKoleksi::delete/$1');

	//custom
	$subroutes->add('datatable', 'SumberKoleksi::datatable');
	$subroutes->add('datatable/(:any)', 'SumberKoleksi::datatable/$1');
});
