<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('pemesanankoleksi', ['namespace' => 'PemesananKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PemesananKoleksi::index');
	$subroutes->add('index', 'PemesananKoleksi::index');
	$subroutes->add('detail/(:any)', 'PemesananKoleksi::detail/$1');
	$subroutes->add('create', 'PemesananKoleksi::create');
	$subroutes->add('edit/(:any)', 'PemesananKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'PemesananKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'PemesananKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'PemesananKoleksi::do_init');
	$subroutes->add('do_upload', 'PemesananKoleksi::do_upload');
	$subroutes->add('do_delete', 'PemesananKoleksi::do_delete');
	$subroutes->add('flip', 'PemesananKoleksi::flip');
});

$routes->group('api/pemesanankoleksi', ['namespace' => 'PemesananKoleksi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'PemesananKoleksi::detail/$1');
	$subroutes->add('create', 'PemesananKoleksi::create');
	$subroutes->add('edit/(:any)', 'PemesananKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'PemesananKoleksi::delete/$1');
});