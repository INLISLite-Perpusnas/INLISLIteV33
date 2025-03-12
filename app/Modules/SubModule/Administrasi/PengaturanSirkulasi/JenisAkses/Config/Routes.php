<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jenis-akses', ['namespace' => 'JenisAkses\Controllers'], function ($subroutes) {
	$subroutes->add('', 'JenisAkses::index');
	$subroutes->add('index', 'JenisAkses::index');
	$subroutes->add('detail/(:any)', 'JenisAkses::detail/$1');
	$subroutes->add('create', 'JenisAkses::create');
	$subroutes->add('edit/(:any)', 'JenisAkses::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisAkses::delete/$1');
	$subroutes->add('apply_status/(:any)', 'JenisAkses::apply_status/$1');
	$subroutes->add('do_init', 'JenisAkses::do_init');
	$subroutes->add('do_upload', 'JenisAkses::do_upload');
	$subroutes->add('do_delete', 'JenisAkses::do_delete');
	$subroutes->add('flip', 'JenisAkses::flip');
});

$routes->group('api/master-jenis-akses', ['namespace' => 'JenisAkses\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'JenisAkses::detail/$1');
	$subroutes->add('create', 'JenisAkses::create');
	$subroutes->add('edit/(:any)', 'JenisAkses::edit/$1');
	$subroutes->add('delete/(:any)', 'JenisAkses::delete/$1');

	//custom
	$subroutes->add('datatable', 'JenisAkses::datatable');
	$subroutes->add('datatable/(:any)', 'JenisAkses::datatable/$1');
});
