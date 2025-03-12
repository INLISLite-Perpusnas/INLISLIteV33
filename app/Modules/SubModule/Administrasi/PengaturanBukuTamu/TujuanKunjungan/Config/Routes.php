<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-tujuan-kunjungan', ['namespace' => 'TujuanKunjungan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'TujuanKunjungan::index');
	$subroutes->add('index', 'TujuanKunjungan::index');
	$subroutes->add('detail/(:any)', 'TujuanKunjungan::detail/$1');
	$subroutes->add('create', 'TujuanKunjungan::create');
	$subroutes->add('edit/(:any)', 'TujuanKunjungan::edit/$1');
	$subroutes->add('delete/(:any)', 'TujuanKunjungan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'TujuanKunjungan::apply_status/$1');
	$subroutes->add('do_init', 'TujuanKunjungan::do_init');
	$subroutes->add('do_upload', 'TujuanKunjungan::do_upload');
	$subroutes->add('do_delete', 'TujuanKunjungan::do_delete');
	$subroutes->add('flip', 'TujuanKunjungan::flip');
});

$routes->group('api/tujuan-kunjungan', ['namespace' => 'TujuanKunjungan\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'TujuanKunjungan::detail/$1');
	$subroutes->add('create', 'TujuanKunjungan::create');
	$subroutes->add('edit/(:any)', 'TujuanKunjungan::edit/$1');
	$subroutes->add('delete/(:any)', 'TujuanKunjungan::delete/$1');

	//custom
	$subroutes->add('datatable', 'TujuanKunjungan::datatable');
	$subroutes->add('datatable/(:any)', 'TujuanKunjungan::datatable/$1');
});
