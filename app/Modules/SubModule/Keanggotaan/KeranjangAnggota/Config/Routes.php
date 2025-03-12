<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('keranjanganggota', ['namespace' => 'KeranjangAnggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'KeranjangAnggota::index');
	$subroutes->add('index', 'KeranjangAnggota::index');
	$subroutes->add('detail/(:any)', 'KeranjangAnggota::detail/$1');
	$subroutes->add('create', 'KeranjangAnggota::create');
	$subroutes->add('edit/(:any)', 'KeranjangAnggota::edit/$1');
	$subroutes->add('delete/(:any)', 'KeranjangAnggota::delete/$1');
	$subroutes->add('apply_status/(:any)', 'KeranjangAnggota::apply_status/$1');
	$subroutes->add('do_init', 'KeranjangAnggota::do_init');
	$subroutes->add('do_upload', 'KeranjangAnggota::do_upload');
	$subroutes->add('do_delete', 'KeranjangAnggota::do_delete');
	$subroutes->add('flip', 'KeranjangAnggota::flip');
});

$routes->group('api/keranjanganggota', ['namespace' => 'KeranjangAnggota\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'KeranjangAnggota::detail/$1');
	$subroutes->add('create', 'KeranjangAnggota::create');
	$subroutes->add('edit/(:any)', 'KeranjangAnggota::edit/$1');
	$subroutes->add('delete/(:any)', 'KeranjangAnggota::delete/$1');
});