<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('report', ['namespace' => 'Eksemplar\Controllers'], function ($subroutes) {
	$subroutes->add('eksemplar', 'Eksemplar::report');
});

$routes->group('eksemplar', ['namespace' => 'Eksemplar\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Eksemplar::index');
	$subroutes->add('index', 'Eksemplar::index');
	$subroutes->add('detail/(:any)', 'Eksemplar::detail/$1');
	$subroutes->add('create', 'Eksemplar::create');
	$subroutes->add('edit/(:any)', 'Eksemplar::edit/$1');
	$subroutes->add('delete/(:any)', 'Eksemplar::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Eksemplar::apply_status/$1');
	$subroutes->add('do_init', 'Eksemplar::do_init');
	$subroutes->add('do_upload', 'Eksemplar::do_upload');
	$subroutes->add('do_delete', 'Eksemplar::do_delete');
	$subroutes->add('proses_karantina', 'Eksemplar::proses_karantina');
	$subroutes->add('pulihkan_eksemplar', 'Eksemplar::pulihkan_eksemplar');
	$subroutes->add('flip', 'Eksemplar::flip');

	//custom
	$subroutes->add('print_label', 'Eksemplar::print_label');
});

$routes->group('api/eksemplar', ['namespace' => 'Eksemplar\Controllers\Api'], function ($subroutes) {
	$subroutes->add('', 'Eksemplar::index');
	$subroutes->add('index', 'Eksemplar::index');
	$subroutes->add('index/(:any)', 'Eksemplar::index/$1');
	$subroutes->add('detail/(:any)', 'Eksemplar::detail/$1');
	$subroutes->add('create', 'Eksemplar::create');
	$subroutes->add('add_partner', 'Eksemplar::add_partner');
	$subroutes->add('get_partner/(:any)', 'Eksemplar::get_partner/$1');
	$subroutes->add('edit_partner/(:any)', 'Eksemplar::edit_partner/$1');
	$subroutes->add('edit/(:any)', 'Eksemplar::edit/$1');
	$subroutes->add('delete/(:any)', 'Eksemplar::delete/$1');

	//custom
	$subroutes->add('datatable', 'Eksemplar::datatable');
	
	$subroutes->add('datatable/(:any)', 'Eksemplar::datatable/$1');
	$subroutes->add('datatable/(:any)/(:any)', 'Eksemplar::datatable/$1/$2');
	$subroutes->add('switch/(:any)', 'Eksemplar::switch/$1');

	$subroutes->get('collectionsources', 'Eksemplar::get_collectionsources');
	$subroutes->get('collectionpartners', 'Eksemplar::get_collectionpartners');
	$subroutes->get('collectionrules', 'Eksemplar::get_collectionrules');
	$subroutes->get('collectionmedias', 'Eksemplar::get_collectionmedias');
	$subroutes->get('collectioncategory', 'Eksemplar::get_collectioncategory');
	$subroutes->get('collectionstatus', 'Eksemplar::get_collectionstatus');
	$subroutes->get('collectioncurrency', 'Eksemplar::get_collectioncurrency');
	$subroutes->get('collectionpricetype', 'Eksemplar::get_collectionpricetype');
	$subroutes->add('katalog', 'Eksemplar::katalog');
	$subroutes->add('locationlibrary', 'Eksemplar::get_locationlibrary');
	$subroutes->add('locations', 'Eksemplar::get_locations/0');
	$subroutes->add('locations/(:any)', 'Eksemplar::get_locations/$1');
	$subroutes->add('eksemplar_number/(:any)', 'Eksemplar::get_eksemplar_number/$1');
});

$routes->group('karantina-eksemplar', ['namespace' => 'Eksemplar\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Eksemplar::karantina');
});