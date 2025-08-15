<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('stockopname', ['namespace' => 'Stockopname\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Stockopname::index');
	$subroutes->add('index', 'Stockopname::index');
	$subroutes->add('detail/(:any)', 'Stockopname::detail/$1');
	$subroutes->add('create', 'Stockopname::create');
	$subroutes->add('store', 'Stockopname::store');
	$subroutes->add('edit/(:any)', 'Stockopname::edit/$1');
	$subroutes->add('delete/(:any)', 'Stockopname::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Stockopname::apply_status/$1');
	  // Barcode scanning and detail management
    $subroutes->add('scanBarcode', 'Stockopname::scanBarcode');
    $subroutes->add('updateDetail', 'Stockopname::updateDetail');
    $subroutes->delete('deleteDetail/(:num)', 'Stockopname::deleteDetail/$1');
    $subroutes->get('getCollectionInfo', 'Stockopname::getCollectionInfo');
    
    // Export functionality
    $subroutes->get('exportStockopname/(:num)', 'Stockopname::exportStockopname/$1');

});

$routes->group('api/stockopname', ['namespace' => 'Stockopname\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Stockopname::detail/$1');
	$subroutes->add('create', 'Stockopname::create');
	$subroutes->add('edit/(:any)', 'Stockopname::edit/$1');
	$subroutes->add('delete/(:any)', 'Stockopname::delete/$1');

	//custom
	$subroutes->add('datatable', 'Stockopname::datatable');
	$subroutes->add('datatable/(:any)', 'Stockopname::datatable/$1');
	$subroutes->add('non_anggota_datatable', 'Stockopname::non_anggota_datatable');
	$subroutes->add('non_anggota_datatable/(:any)', 'Stockopname::non_anggota_datatable/$1');
	$subroutes->add('rombongan_datatable', 'Stockopname::rombongan_datatable');
	$subroutes->add('rombongan_datatable/(:any)', 'Stockopname::rombongan_datatable/$1');
});