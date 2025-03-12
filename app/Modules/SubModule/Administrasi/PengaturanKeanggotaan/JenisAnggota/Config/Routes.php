<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-anggota', ['namespace' => 'JenisAnggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisAnggota::index');
	$subroutes->add('index', 'JenisAnggota::index');
	$subroutes->add('detail/(:any)', 'JenisAnggota::detail/$1');
	$subroutes->add('create', 'JenisAnggota::create');
	$subroutes->post('save', 'JenisAnggota::save');
	$subroutes->post('savejenisbahan', 'JenisAnggota::savejenisbahan');
	// $subroutes->post('save', 'JenisAnggota::delete');
	$subroutes->add('edit/(:any)', 'JenisAnggota::edit/$1');
	$subroutes->delete('deletedefaultlokasi/(:num)', 'JenisAnggota::deletedefaultlokasi/$1');
	$subroutes->delete('deletedefaultbahan/(:num)', 'JenisAnggota::deletedefaultbahan/$1');
	$subroutes->add('DefaultLokasi/(:any)', 'JenisAnggota::DefaultLokasi/$1');
	$subroutes->add('DefaultBahan/(:any)', 'JenisAnggota::DefaultBahan/$1');
	$subroutes->add('delete/(:any)', 'JenisAnggota::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisAnggota::apply_status/$1');
	$subroutes->add('do_init', 'JenisAnggota::do_init');
	$subroutes->add('do_upload', 'JenisAnggota::do_upload');
	$subroutes->add('do_delete', 'JenisAnggota::do_delete');
	$subroutes->add('flip', 'JenisAnggota::flip');
});

$routes->group('api/jenis-anggota', ['namespace' => 'JenisAnggota\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisAnggota::detail/$1');
	$subroutes->add('save', 'JenisAnggota::create');
	$subroutes->add('edit/(:any)', 'JenisAnggota::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisAnggota::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisAnggota::datatable');
	$subroutes->add('datatable/(:any)', 'JenisAnggota::datatable/$1');
	$subroutes->add('switch/(:any)', 'JenisAnggota::switch/$1');
});