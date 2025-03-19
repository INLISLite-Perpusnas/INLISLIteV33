<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-mitra-perpustakaan', ['namespace' => 'MitraPerpustakaan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MitraPerpustakaan::index');
	$subroutes->add('index', 'MitraPerpustakaan::index');
	$subroutes->add('sync', 'MitraPerpustakaan::sync');
	$subroutes->add('detail/(:any)', 'MitraPerpustakaan::detail/$1');
	$subroutes->add('create', 'MitraPerpustakaan::create');
	$subroutes->add('edit/(:any)', 'MitraPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'MitraPerpustakaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MitraPerpustakaan::apply_status/$1');
	$subroutes->add('do_init', 'MitraPerpustakaan::do_init');
	$subroutes->add('do_upload', 'MitraPerpustakaan::do_upload');
	$subroutes->add('do_delete', 'MitraPerpustakaan::do_delete');
	$subroutes->add('flip', 'MitraPerpustakaan::flip');
});

$routes->group('api-mitra-perpustakaan', ['namespace' => 'MitraPerpustakaan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'MitraPerpustakaan::detail/$1');
	$subroutes->add('create', 'MitraPerpustakaan::create');
	$subroutes->add('edit/(:any)', 'MitraPerpustakaan::edit/$1');
	$subroutes->add('delete/(:any)', 'MitraPerpustakaan::delete/$1');
	$subroutes->add('location_library/(:any)', 'MitraPerpustakaan::location_library/$1');

	//custom
	$subroutes->add('datatable', 'MitraPerpustakaan::datatable');
	$subroutes->add('datatable/(:any)', 'MitraPerpustakaan::datatable/$1');
	$subroutes->get('branch/(:any)', 'MitraPerpustakaan::get_branchs/$1');
	$subroutes->get('branch/(:any)/(:any)', 'MitraPerpustakaan::get_branchs/$1/$2');
	$subroutes->get('check/(:any)', 'MitraPerpustakaan::check/$1');
	$subroutes->add('select2', 'MitraPerpustakaan::select2');
	$subroutes->add('select2/(:any)', 'MitraPerpustakaan::select2/$1');
});