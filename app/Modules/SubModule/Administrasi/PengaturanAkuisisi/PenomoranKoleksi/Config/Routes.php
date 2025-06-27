<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-penomoran-koleksi', ['namespace' => 'PenomoranKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PenomoranKoleksi::index');
	$subroutes->add('index', 'PenomoranKoleksi::index');
	$subroutes->add('detail/(:any)', 'PenomoranKoleksi::detail/$1');
	$subroutes->add('create', 'PenomoranKoleksi::create');
	$subroutes->add('edit/(:any)', 'PenomoranKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'PenomoranKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'PenomoranKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'PenomoranKoleksi::do_init');
	$subroutes->add('do_upload', 'PenomoranKoleksi::do_upload');
	$subroutes->add('do_delete', 'PenomoranKoleksi::do_delete');
	$subroutes->add('flip', 'PenomoranKoleksi::flip');
});

$routes->group('api/master-media-koleksi', ['namespace' => 'PenomoranKoleksi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'PenomoranKoleksi::detail/$1');
	$subroutes->add('create', 'PenomoranKoleksi::create');
	$subroutes->add('edit/(:any)', 'PenomoranKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'PenomoranKoleksi::delete/$1');

	//custom
	$subroutes->add('datatable', 'PenomoranKoleksi::datatable');
	$subroutes->add('datatable/(:any)', 'PenomoranKoleksi::datatable/$1');
});
