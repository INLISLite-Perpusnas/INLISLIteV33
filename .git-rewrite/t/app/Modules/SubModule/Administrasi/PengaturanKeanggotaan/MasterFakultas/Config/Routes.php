<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-fakultas', ['namespace' => 'MasterFakultas\Controllers'], function ($subroutes) {
	$subroutes->add('', 'MasterFakultas::index');
	$subroutes->add('index', 'MasterFakultas::index');
	$subroutes->add('detail/(:any)', 'MasterFakultas::detail/$1');
	$subroutes->add('create', 'MasterFakultas::create');
	$subroutes->add('edit/(:any)', 'MasterFakultas::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterFakultas::delete/$1');
	$subroutes->add('apply_status/(:any)', 'MasterFakultas::apply_status/$1');
	$subroutes->add('do_init', 'MasterFakultas::do_init');
	$subroutes->add('do_upload', 'MasterFakultas::do_upload');
	$subroutes->add('do_delete', 'MasterFakultas::do_delete');
	$subroutes->add('flip', 'MasterFakultas::flip');
});

$routes->group('api/fakultas', ['namespace' => 'MasterFakultas\Controllers\Api'], function ($subroutes) {
	$subroutes->get('', 'MasterFakultas::index');
	$subroutes->add('detail/(:any)', 'MasterFakultas::detail/$1');
	$subroutes->add('create', 'MasterFakultas::create');
	$subroutes->add('edit/(:any)', 'MasterFakultas::edit/$1');
	$subroutes->add('delete/(:any)', 'MasterFakultas::delete/$1');

	//custom
	$subroutes->add('datatable', 'MasterFakultas::datatable');
	$subroutes->add('datatable/(:any)', 'MasterFakultas::datatable/$1');
});