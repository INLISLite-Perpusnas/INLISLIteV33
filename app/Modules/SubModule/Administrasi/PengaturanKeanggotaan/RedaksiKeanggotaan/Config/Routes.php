<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-redaksi-keanggotaan', ['namespace' => 'RedaksiKeanggotaan\Controllers'], function ($subroutes) {$subroutes->add('', 'RedaksiKeanggotaan::index');
	$subroutes->add('index', 'RedaksiKeanggotaan::index');
	$subroutes->add('detail/(:any)', 'RedaksiKeanggotaan::detail/$1');
	$subroutes->add('create', 'RedaksiKeanggotaan::create');
	$subroutes->add('edit/(:any)', 'RedaksiKeanggotaan::edit/$1');
	$subroutes->add('delete/(:any)', 'RedaksiKeanggotaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'RedaksiKeanggotaan::apply_status/$1');
});

$routes->group('api/master-redaksi-keanggotaan', ['namespace' => 'RedaksiKeanggotaan\Controllers\Api'], function ($subroutes) {$subroutes->add('detail/(:any)', 'RedaksiKeanggotaan::detail/$1');
	$subroutes->add('create', 'RedaksiKeanggotaan::create');
	$subroutes->add('edit/(:any)', 'RedaksiKeanggotaan::edit/$1');
	$subroutes->add('delete/(:any)', 'RedaksiKeanggotaan::delete/$1');

	//custom
	$subroutes->add('datatable', 'RedaksiKeanggotaan::datatable');
	$subroutes->add('datatable/(:any)', 'RedaksiKeanggotaan::datatable/$1');
	$subroutes->add('switch/(:any)', 'RedaksiKeanggotaan::switch/$1');
	$subroutes->add('field_data_delete/(:any)', 'RedaksiKeanggotaan::field_data_delete/$1');
	$subroutes->add('field_indicator1_delete/(:any)', 'RedaksiKeanggotaan::field_indicator1_delete/$1');
	$subroutes->add('field_indicator2_delete/(:any)', 'RedaksiKeanggotaan::field_indicator2_delete/$1');
});