<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-peraturan-peminjaman-hari', ['namespace' => 'PeraturanPeminjamanHari\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PeraturanPeminjamanHari::index');
	$subroutes->add('index', 'PeraturanPeminjamanHari::index');
	$subroutes->add('detail/(:any)', 'PeraturanPeminjamanHari::detail/$1');
	$subroutes->add('create', 'PeraturanPeminjamanHari::create');
	$subroutes->add('edit/(:any)', 'PeraturanPeminjamanHari::edit/$1');
	$subroutes->add('delete/(:any)', 'PeraturanPeminjamanHari::delete/$1');
	$subroutes->add('apply_status/(:any)', 'PeraturanPeminjamanHari::apply_status/$1');
	$subroutes->add('do_init', 'PeraturanPeminjamanHari::do_init');
	$subroutes->add('do_upload', 'PeraturanPeminjamanHari::do_upload');
	$subroutes->add('do_delete', 'PeraturanPeminjamanHari::do_delete');
	$subroutes->add('flip', 'PeraturanPeminjamanHari::flip');
});

$routes->group('api/peraturan-peminjaman-hari', ['namespace' => 'PeraturanPeminjamanHari\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'PeraturanPeminjamanHari::detail/$1');
	$subroutes->add('create', 'PeraturanPeminjamanHari::create');
	$subroutes->add('edit/(:any)', 'PeraturanPeminjamanHari::edit/$1');
	$subroutes->add('delete/(:any)', 'PeraturanPeminjamanHari::delete/$1');

	//custom
	$subroutes->add('datatable', 'PeraturanPeminjamanHari::datatable');
	$subroutes->add('datatable/(:any)', 'PeraturanPeminjamanHari::datatable/$1');
});