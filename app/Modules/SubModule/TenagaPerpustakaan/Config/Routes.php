<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('tenaga-perpustakaan', ['namespace' => 'TenagaPerpustakaan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'TenagaPerpustakaan::index');
	$subroutes->add('index', 'TenagaPerpustakaan::index');
	$subroutes->add('datatable', 'TenagaPerpustakaan::datatable');
	$subroutes->add('detail/(:any)', 'TenagaPerpustakaan::detail/$1');
	$subroutes->add('create', 'TenagaPerpustakaan::create');
	$subroutes->add('store', 'TenagaPerpustakaan::store');
	$subroutes->add('edit/(:any)', 'TenagaPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'TenagaPerpustakaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'TenagaPerpustakaan::apply_status/$1');
	$subroutes->add('do_init', 'TenagaPerpustakaan::do_init');
	$subroutes->add('do_upload', 'TenagaPerpustakaan::do_upload');
	$subroutes->add('do_delete', 'TenagaPerpustakaan::do_delete');
	$subroutes->add('flip', 'TenagaPerpustakaan::flip');

	//custom
	$subroutes->add('non_anggota', 'TenagaPerpustakaan::non_anggota');
	$subroutes->add('rombongan', 'TenagaPerpustakaan::rombongan');
});

$routes->group('api/TenagaPerpustakaan', ['namespace' => 'TenagaPerpustakaan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'TenagaPerpustakaan::detail/$1');
	$subroutes->add('create', 'TenagaPerpustakaan::create');
	$subroutes->add('edit/(:any)', 'TenagaPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'TenagaPerpustakaan::delete/$1');

	//custom
	$subroutes->add('datatable', 'TenagaPerpustakaan::datatable');
	$subroutes->add('datatable/(:any)', 'TenagaPerpustakaan::datatable/$1');
	$subroutes->add('non_anggota_datatable', 'TenagaPerpustakaan::non_anggota_datatable');
	$subroutes->add('non_anggota_datatable/(:any)', 'TenagaPerpustakaan::non_anggota_datatable/$1');
	$subroutes->add('rombongan_datatable', 'TenagaPerpustakaan::rombongan_datatable');
	$subroutes->add('rombongan_datatable/(:any)', 'TenagaPerpustakaan::rombongan_datatable/$1');
});