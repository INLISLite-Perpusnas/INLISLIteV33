<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-kategori-koleksi', ['namespace' => 'KategoriKoleksi\Controllers'], function ($subroutes) {
	$subroutes->add('', 'KategoriKoleksi::index');
	$subroutes->add('index', 'KategoriKoleksi::index');
	$subroutes->add('detail/(:any)', 'KategoriKoleksi::detail/$1');
	$subroutes->add('create', 'KategoriKoleksi::create');
	$subroutes->add('edit/(:any)', 'KategoriKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'KategoriKoleksi::delete/$1');
	$subroutes->add('apply_status/(:any)', 'KategoriKoleksi::apply_status/$1');
	$subroutes->add('do_init', 'KategoriKoleksi::do_init');
	$subroutes->add('do_upload', 'KategoriKoleksi::do_upload');
	$subroutes->add('do_delete', 'KategoriKoleksi::do_delete');
	$subroutes->add('flip', 'KategoriKoleksi::flip');
});

$routes->group('api/master-kategori-koleksi', ['namespace' => 'KategoriKoleksi\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'KategoriKoleksi::detail/$1');
	$subroutes->add('create', 'KategoriKoleksi::create');
	$subroutes->add('edit/(:any)', 'KategoriKoleksi::edit/$1');
	$subroutes->add('delete/(:any)', 'KategoriKoleksi::delete/$1');

	//custom
	$subroutes->add('datatable', 'KategoriKoleksi::datatable');
	$subroutes->add('datatable/(:any)', 'KategoriKoleksi::datatable/$1');
});
