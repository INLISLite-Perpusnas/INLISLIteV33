<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-kelas-besar', ['namespace' => 'MasterKelasBesar\Controllers'], function($routes) {
    $routes->get('/', 'MasterKelasBesar::index');
    $routes->post('create', 'MasterKelasBesar::create');
    $routes->get('detail/(:num)', 'MasterKelasBesar::detail/$1');
    $routes->post('update/(:num)', 'MasterKelasBesar::update/$1');
    $routes->get('delete/(:num)', 'MasterKelasBesar::delete/$1');
    $routes->get('apply_status/(:num)', 'MasterKelasBesar::apply_status/$1');
    $routes->get('datatable/(:any)', 'MasterKelasBesar::datatable/$1');
    $routes->get('datatable', 'MasterKelasBesar::datatable');
});
$routes->group('api/master-kelas-besar', ['namespace' => 'MasterKelasBesar\Controllers\Api'], function ($subroutes) {
	$subroutes->add('get_all_MasterKelasBesars', 'MasterKelasBesar::get_all_MasterKelasBesars');
	$subroutes->add('datatable', 'MasterKelasBesar::datatable');
	$subroutes->add('datatable/(:any)', 'MasterKelasBesar::datatable/$1');
	$subroutes->add('switch/(:any)', 'MasterKelasBesar::switch/$1');
	$subroutes->add('field_data_delete/(:any)', 'MasterKelasBesar::field_data_delete/$1');
	$subroutes->add('field_indicator1_delete/(:any)', 'MasterKelasBesar::field_indicator1_delete/$1');
	$subroutes->add('field_indicator2_delete/(:any)', 'MasterKelasBesar::field_indicator2_delete/$1');
});
