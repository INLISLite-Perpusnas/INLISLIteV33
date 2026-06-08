<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-unit-kerja', ['namespace' => 'UnitKerja\Controllers'], function ($subroutes) {
	$subroutes->add('', 'UnitKerja::index');
	$subroutes->add('index', 'UnitKerja::index');
	$subroutes->add('detail/(:any)', 'UnitKerja::detail/$1');
	$subroutes->add('create', 'UnitKerja::create');
	$subroutes->add('edit/(:any)', 'UnitKerja::edit/$1');
	$subroutes->add('delete/(:any)', 'UnitKerja::delete/$1');
	$subroutes->add('apply_status/(:any)', 'UnitKerja::apply_status/$1');
	$subroutes->add('do_init', 'UnitKerja::do_init');
	$subroutes->add('do_upload', 'UnitKerja::do_upload');
	$subroutes->add('do_delete', 'UnitKerja::do_delete');
	$subroutes->add('flip', 'UnitKerja::flip');
});

$routes->group('api/unit-kerja', ['namespace' => 'UnitKerja\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'UnitKerja::detail/$1');
	$subroutes->add('create', 'UnitKerja::create');
	$subroutes->add('edit/(:any)', 'UnitKerja::edit/$1');
	$subroutes->add('delete/(:any)', 'UnitKerja::delete/$1');

	//custom
	$subroutes->add('datatable', 'UnitKerja::datatable');
	$subroutes->add('datatable/(:any)', 'UnitKerja::datatable/$1');
});