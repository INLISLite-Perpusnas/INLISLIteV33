<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('usulankoleksi', ['namespace' => 'UsulanKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'UsulanKoleksi::index');
	$subroutes->add('index', 'UsulanKoleksi::index');
	$subroutes->add('detail/(:any)', 'UsulanKoleksi::detail/$1');
	$subroutes->add('create', 'UsulanKoleksi::create');
	$subroutes->add('edit/(:any)', 'UsulanKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'UsulanKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'UsulanKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'UsulanKoleksi::do_init');
	$subroutes->add('do_upload', 'UsulanKoleksi::do_upload');
	$subroutes->add('do_delete', 'UsulanKoleksi::do_delete');
	$subroutes->add('flip', 'UsulanKoleksi::flip');
});

$routes->group('api/usulankoleksi', ['namespace' => 'UsulanKoleksi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'UsulanKoleksi::detail/$1');
	$subroutes->add('create', 'UsulanKoleksi::create');
	$subroutes->add('edit/(:any)', 'UsulanKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'UsulanKoleksi::delete/$1');
});