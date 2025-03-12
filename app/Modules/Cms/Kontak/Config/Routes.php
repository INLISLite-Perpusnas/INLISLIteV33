<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/kontak', ['namespace' => 'Kontak\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Kontak::index');
	$subroutes->add('index', 'Kontak::index');
	$subroutes->add('detail/(:any)', 'Kontak::detail/$1');
	$subroutes->add('edit/(:any)', 'Kontak::edit/$1');
	$subroutes->add('create', 'Kontak::create');
	$subroutes->add('delete/(:any)', 'Kontak::delete/$1');
	$subroutes->add('do_init', 'Kontak::do_init');
	$subroutes->add('do_upload', 'Kontak::do_upload');
	$subroutes->add('do_delete', 'Kontak::do_delete');
	$subroutes->add('flip', 'Kontak::flip');
	$subroutes->add('apply_status/(:any)', 'Kontak::apply_status/$1');
	$subroutes->add('export', 'Kontak::export');
	$subroutes->add('thumb', 'Kontak::thumb');
});

$routes->group('api/kontak', ['namespace' => 'Kontak\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Kontak::index');
	$subroutes->add('index', 'Kontak::index');
	$subroutes->add('detail/(:any)', 'Kontak::detail/$1');
	$subroutes->add('show/(:any)', 'Kontak::show/$1');
	$subroutes->add('create', 'Kontak::create');
	$subroutes->add('update/(:any)', 'Kontak::update/$1');
	$subroutes->add('delete/(:any)', 'Kontak::delete/$1');

	//custom
	$subroutes->add('datatable', 'Kontak::datatable');
	$subroutes->add('datatable/(:any)', 'Kontak::datatable/$1');
	$subroutes->add('upload_file', 'Kontak::upload_file');
});
