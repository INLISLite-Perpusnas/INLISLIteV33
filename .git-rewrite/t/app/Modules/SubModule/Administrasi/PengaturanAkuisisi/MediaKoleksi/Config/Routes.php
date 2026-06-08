<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-media-koleksi', ['namespace' => 'MediaKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MediaKoleksi::index');
	$subroutes->add('index', 'MediaKoleksi::index');
	$subroutes->add('detail/(:any)', 'MediaKoleksi::detail/$1');
	$subroutes->add('create', 'MediaKoleksi::create');
	$subroutes->add('edit/(:any)', 'MediaKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'MediaKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MediaKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'MediaKoleksi::do_init');
	$subroutes->add('do_upload', 'MediaKoleksi::do_upload');
	$subroutes->add('do_delete', 'MediaKoleksi::do_delete');
	$subroutes->add('flip', 'MediaKoleksi::flip');
});

$routes->group('api/master-media-koleksi', ['namespace' => 'MediaKoleksi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'MediaKoleksi::detail/$1');
	$subroutes->add('create', 'MediaKoleksi::create');
	$subroutes->add('edit/(:any)', 'MediaKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'MediaKoleksi::delete/$1');

	//custom
	$subroutes->add('datatable', 'MediaKoleksi::datatable');
	$subroutes->add('datatable/(:any)', 'MediaKoleksi::datatable/$1');
});
