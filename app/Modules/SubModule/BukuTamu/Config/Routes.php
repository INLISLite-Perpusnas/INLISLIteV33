<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('bukutamu', ['namespace' => 'BukuTamu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'BukuTamu::index');
	$subroutes->add('index', 'BukuTamu::index');
	$subroutes->add('detail/(:any)', 'BukuTamu::detail/$1');
	$subroutes->add('create', 'BukuTamu::create');
	$subroutes->add('edit/(:any)', 'BukuTamu::edit/$1');
	$subroutes->add('delete/(:any)', 'BukuTamu::delete/$1');
	$subroutes->add('apply_status/(:any)', 'BukuTamu::apply_status/$1');
	$subroutes->add('do_init', 'BukuTamu::do_init');
	$subroutes->add('do_upload', 'BukuTamu::do_upload');
	$subroutes->add('do_delete', 'BukuTamu::do_delete');
	$subroutes->add('flip', 'BukuTamu::flip');

	//custom
	$subroutes->add('non_anggota', 'BukuTamu::non_anggota');
	$subroutes->add('rombongan', 'BukuTamu::rombongan');
});

$routes->group('api/bukutamu', ['namespace' => 'BukuTamu\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'BukuTamu::detail/$1');
	$subroutes->add('create', 'BukuTamu::create');
	$subroutes->add('edit/(:any)', 'BukuTamu::edit/$1');
	$subroutes->add('delete/(:any)', 'BukuTamu::delete/$1');

	//custom
	$subroutes->add('datatable', 'BukuTamu::datatable');
	$subroutes->add('datatable/(:any)', 'BukuTamu::datatable/$1');
	$subroutes->add('non_anggota_datatable', 'BukuTamu::non_anggota_datatable');
	$subroutes->add('non_anggota_datatable/(:any)', 'BukuTamu::non_anggota_datatable/$1');
	$subroutes->add('rombongan_datatable', 'BukuTamu::rombongan_datatable');
	$subroutes->add('rombongan_datatable/(:any)', 'BukuTamu::rombongan_datatable/$1');
});