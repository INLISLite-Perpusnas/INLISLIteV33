<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-peraturan-peminjaman-tanggal', ['namespace' => 'PeraturanPeminjamanTanggal\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PeraturanPeminjamanTanggal::index');
	$subroutes->add('index', 'PeraturanPeminjamanTanggal::index');
	$subroutes->add('detail/(:any)', 'PeraturanPeminjamanTanggal::detail/$1');
	$subroutes->add('create', 'PeraturanPeminjamanTanggal::create');
	$subroutes->add('edit/(:any)', 'PeraturanPeminjamanTanggal::edit/$1');
	$subroutes->add('delete/(:any)', 'PeraturanPeminjamanTanggal::delete/$1');
	$subroutes->add('apply_status/(:any)', 'PeraturanPeminjamanTanggal::apply_status/$1');
	$subroutes->add('do_init', 'PeraturanPeminjamanTanggal::do_init');
	$subroutes->add('do_upload', 'PeraturanPeminjamanTanggal::do_upload');
	$subroutes->add('do_delete', 'PeraturanPeminjamanTanggal::do_delete');
	$subroutes->add('flip', 'PeraturanPeminjamanTanggal::flip');
});

$routes->group('api/peraturan-peminjaman-tanggal', ['namespace' => 'PeraturanPeminjamanTanggal\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'PeraturanPeminjamanTanggal::detail/$1');
	$subroutes->add('create', 'PeraturanPeminjamanTanggal::create');
	$subroutes->add('edit/(:any)', 'PeraturanPeminjamanTanggal::edit/$1');
	$subroutes->add('delete/(:any)', 'PeraturanPeminjamanTanggal::delete/$1');

	//custom
	$subroutes->add('datatable', 'PeraturanPeminjamanTanggal::datatable');
	$subroutes->add('datatable/(:any)', 'PeraturanPeminjamanTanggal::datatable/$1');
});