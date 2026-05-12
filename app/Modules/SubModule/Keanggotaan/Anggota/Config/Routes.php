<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('report', ['namespace' => 'Anggota\Controllers'], function ($subroutes) {
	$subroutes->add('anggota', 'Anggota::report');
});

$routes->get('profil_anggota', 'Anggota::profile', ['namespace' => 'Anggota\Controllers']);
$routes->post('profil_anggota', 'Anggota::edit', ['namespace' => 'Anggota\Controllers']);

$routes->group('anggota', ['namespace' => 'Anggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Anggota::index');
	$subroutes->add('index', 'Anggota::index');
	$subroutes->add('online', 'Anggota::online');
	$subroutes->add('profile', 'Anggota::profile');
	$subroutes->add('index_json', 'Anggota::index_json');
	$subroutes->add('keranjang', 'Anggota::keranjang');
	$subroutes->add('index_datatables', 'Anggota::index_datatables');
	$subroutes->add('ajaxDataTables', 'Anggota::ajaxDataTables');
	$subroutes->add('json', 'Anggota::json');
	$subroutes->add('ajaxDataAnggota', 'Anggota::ajaxDataAnggota');
	$subroutes->add('detail/(:any)', 'Anggota::detail/$1');
	$subroutes->add('create', 'Anggota::create');
	$subroutes->add('edit', 'Anggota::edit');
	$subroutes->add('edit/(:any)', 'Anggota::edit/$1');
	$subroutes->add('edit/(:any)/(:any)', 'Anggota::edit/$1/$2');
	$subroutes->add('delete/(:any)', 'Anggota::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Anggota::apply_status/$1');
	$subroutes->add('do_init', 'Anggota::do_init');
	$subroutes->add('do_upload', 'Anggota::do_upload');
	$subroutes->add('do_delete', 'Anggota::do_delete');
	$subroutes->add('flip', 'Anggota::flip');
	$subroutes->add('import', 'Anggota::import');
	$subroutes->add('aktifkan_online', 'Anggota::aktifkan_online');
	$subroutes->add('uploadBackground', 'Anggota::uploadBackground');
	$subroutes->add('import_view', 'Anggota::import_view');
	$subroutes->add('cetak-kartu/(:any)', 'Anggota::print_card2/$1');
	$subroutes->add('print_card/(:any)', 'Anggota::print_card/$1');
	$subroutes->add('printanggota/(:any)', 'Anggota::printanggota/$1');
	$subroutes->add('printkartubelakang/(:any)', 'Anggota::printkartubelakang/$1');
	$subroutes->add('multipleprint', 'Anggota::multipleprint');
	$subroutes->add('bebaspustaka/(:any)', 'Anggota::bebaspustaka/$1');
	$subroutes->add('get_defaults/(:num)', 'Anggota::getDefaults/$1');
	$subroutes->add('proses_keranjang', 'Anggota::proses_keranjang');
	$subroutes->add('pulihkan_keranjang', 'Anggota::pulihkan_keranjang');
	$subroutes->add('hapus_permanen', 'Anggota::hapus_permanen');
});

$routes->group('api/anggota', ['namespace' => 'Anggota\Controllers\Api'], function ($subroutes) {
	$subroutes->add('', 'Anggota::index');
	$subroutes->add('detail/(:any)', 'Anggota::detail/$1');
	$subroutes->add('create', 'Anggota::create');
	$subroutes->add('edit/(:any)', 'Anggota::edit/$1');
	$subroutes->add('delete/(:any)', 'Anggota::delete/$1');
	$subroutes->add('hapusall', 'Anggota::hapusall');
	$subroutes->add('cities', 'Anggota::cities');
	$subroutes->add('get_date', 'Anggota::get_date');

	//custom
	$subroutes->add('datatable', 'Anggota::datatable');
	$subroutes->add('datatable/(:any)', 'Anggota::datatable/$1');
	$subroutes->add('switch/(:any)', 'Anggota::switch/$1');
	$subroutes->add('upload_file', 'Anggota::upload_file');
	$subroutes->add('capture_file', 'Anggota::capture_file');
});

$routes->group('keranjang-anggota', ['namespace' => 'Anggota\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Anggota::keranjang');
});
