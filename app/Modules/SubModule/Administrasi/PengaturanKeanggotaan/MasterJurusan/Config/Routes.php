<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-jurusan', ['namespace' => 'MasterJurusan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MasterJurusan::index');
	$subroutes->add('index', 'MasterJurusan::index');
	$subroutes->add('detail/(:any)', 'MasterJurusan::detail/$1');
	$subroutes->add('create', 'MasterJurusan::create');
	$subroutes->add('edit/(:any)', 'MasterJurusan::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterJurusan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MasterJurusan::apply_status/$1');
	$subroutes->add('do_init', 'MasterJurusan::do_init');
	$subroutes->add('do_upload', 'MasterJurusan::do_upload');
	$subroutes->add('do_delete', 'MasterJurusan::do_delete');
	$subroutes->add('flip', 'MasterJurusan::flip');
});

$routes->group('api/jurusan', ['namespace' => 'MasterJurusan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'MasterJurusan::index');
	$subroutes->add('detail/(:any)', 'MasterJurusan::detail/$1');
	$subroutes->add('create', 'MasterJurusan::create');
	$subroutes->add('edit/(:any)', 'MasterJurusan::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterJurusan::delete/$1');

	//custom
	$subroutes->add('datatable', 'MasterJurusan::datatable');
	$subroutes->add('datatable/(:any)', 'MasterJurusan::datatable/$1');
});