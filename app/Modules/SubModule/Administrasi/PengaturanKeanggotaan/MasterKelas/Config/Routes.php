<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master/kelas', ['namespace' => 'MasterKelas\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MasterKelas::index');
	$subroutes->add('index', 'MasterKelas::index');
	$subroutes->add('detail/(:any)', 'MasterKelas::detail/$1');
	$subroutes->add('create', 'MasterKelas::create');
	$subroutes->add('edit/(:any)', 'MasterKelas::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterKelas::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MasterKelas::apply_status/$1');
	$subroutes->add('do_init', 'MasterKelas::do_init');
	$subroutes->add('do_upload', 'MasterKelas::do_upload');
	$subroutes->add('do_delete', 'MasterKelas::do_delete');
	$subroutes->add('flip', 'MasterKelas::flip');
});

$routes->group('api/kelas', ['namespace' => 'MasterKelas\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'MasterKelas::index');
	$subroutes->add('detail/(:any)', 'MasterKelas::detail/$1');
	$subroutes->add('create', 'MasterKelas::create');
	$subroutes->add('edit/(:any)', 'MasterKelas::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterKelas::delete/$1');

	//custom
	$subroutes->add('datatable', 'MasterKelas::datatable');
	$subroutes->add('datatable/(:any)', 'MasterKelas::datatable/$1');
});