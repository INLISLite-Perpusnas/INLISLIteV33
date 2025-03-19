<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-pengaturan-detail-katalog', ['namespace' => 'PengaturanDetailKatalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'PengaturanDetailKatalog::index');
	$subroutes->add('index', 'PengaturanDetailKatalog::index');
	$subroutes->add('create', 'PengaturanDetailKatalog::create');
	$subroutes->add('edit/(:any)', 'PengaturanDetailKatalog::edit/$1');
	$subroutes->add('delete/(:any)', 'PengaturanDetailKatalog::delete/$1');
});

$routes->group('api/master-pengaturan-detail-katalog', ['namespace' => 'PengaturanDetailKatalog\Controllers\Api'], function ($subroutes) {
	$subroutes->add('datatable', 'PengaturanDetailKatalog::datatable');
	$subroutes->add('datatable/(:any)', 'PengaturanDetailKatalog::datatable/$1');
	$subroutes->add('switch/(:any)', 'PengaturanDetailKatalog::switch/$1');
	$subroutes->add('scd_create/(:any)', 'PengaturanDetailKatalog::scd_create/$1');
	$subroutes->add('scd_delete/(:any)', 'PengaturanDetailKatalog::scd_delete/$1');
});
