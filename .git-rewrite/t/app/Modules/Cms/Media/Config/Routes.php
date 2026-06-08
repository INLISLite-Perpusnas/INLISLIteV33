<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/media', ['namespace' => 'Media\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Media::index');
	$subroutes->add('index', 'Media::index');
	$subroutes->add('detail/(:any)', 'Media::detail/$1');
	$subroutes->add('edit/(:any)', 'Media::edit/$1');
	$subroutes->add('create', 'Media::create');
	$subroutes->add('delete/(:any)', 'Media::delete/$1');
	$subroutes->add('do_init', 'Media::do_init');
	$subroutes->add('do_upload', 'Media::do_upload');
	$subroutes->add('do_delete', 'Media::do_delete');
	$subroutes->add('flip', 'Media::flip');
	$subroutes->add('apply_status/(:any)', 'Media::apply_status/$1');
	$subroutes->add('export', 'Media::export');
	$subroutes->add('thumb', 'Media::thumb');
});

$routes->group('api/media', ['namespace' => 'Media\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Media::index');
	$subroutes->add('index', 'Media::index');
	$subroutes->add('detail/(:any)', 'Media::detail/$1');
	$subroutes->add('show/(:any)', 'Media::show/$1');
	$subroutes->add('create', 'Media::create');
	$subroutes->add('update/(:any)', 'Media::update/$1');
	$subroutes->add('delete/(:any)', 'Media::delete/$1');

	//custom
	$subroutes->add('datatable', 'Media::datatable');
	$subroutes->add('datatable/(:any)', 'Media::datatable/$1');
	$subroutes->add('(:any)', 'Media::detail/$1');
});
