<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-kelompok-umur', ['namespace' => 'MasterKelompokUmur\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MasterKelompokUmur::index');
	$subroutes->add('index', 'MasterKelompokUmur::index');
	$subroutes->add('detail/(:any)', 'MasterKelompokUmur::detail/$1');
	$subroutes->add('create', 'MasterKelompokUmur::create');
	$subroutes->add('edit/(:any)', 'MasterKelompokUmur::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterKelompokUmur::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MasterKelompokUmur::apply_status/$1');
	$subroutes->add('do_init', 'MasterKelompokUmur::do_init');
	$subroutes->add('do_upload', 'MasterKelompokUmur::do_upload');
	$subroutes->add('do_delete', 'MasterKelompokUmur::do_delete');
	$subroutes->add('flip', 'MasterKelompokUmur::flip');
});

$routes->group('api/master-kelompok-umur', ['namespace' => 'MasterKelompokUmur\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'MasterKelompokUmur::index');
	$subroutes->add('detail/(:any)', 'MasterKelompokUmur::detail/$1');
	$subroutes->add('create', 'MasterKelompokUmur::create');
	$subroutes->add('edit/(:any)', 'MasterKelompokUmur::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterKelompokUmur::delete/$1');

	//custom
	$subroutes->add('datatable', 'MasterKelompokUmur::datatable');
	$subroutes->add('datatable/(:any)', 'MasterKelompokUmur::datatable/$1');
});