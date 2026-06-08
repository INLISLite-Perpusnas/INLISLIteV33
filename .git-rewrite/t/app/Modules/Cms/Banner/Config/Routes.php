<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/banner', ['namespace' => 'Banner\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Banner::index');
	$subroutes->add('index', 'Banner::index');
	$subroutes->add('detail/(:any)', 'Banner::detail/$1');
	$subroutes->add('edit/(:any)', 'Banner::edit/$1');
	$subroutes->add('create', 'Banner::create');
	$subroutes->add('delete/(:any)', 'Banner::delete/$1');
	$subroutes->add('do_init', 'Banner::do_init');
	$subroutes->add('do_upload', 'Banner::do_upload');
	$subroutes->add('do_delete', 'Banner::do_delete');
	$subroutes->add('flip', 'Banner::flip');
	$subroutes->add('apply_status/(:any)', 'Banner::apply_status/$1');
	$subroutes->add('export', 'Banner::export');
	$subroutes->add('thumb', 'Banner::thumb');
});

$routes->group('api/banner', ['namespace' => 'Banner\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Banner::index');
	$subroutes->add('index', 'Banner::index');
	$subroutes->add('detail/(:any)', 'Banner::detail/$1');
	$subroutes->add('show/(:any)', 'Banner::show/$1');
	$subroutes->add('create', 'Banner::create');
	$subroutes->add('update/(:any)', 'Banner::update/$1');
	$subroutes->add('delete/(:any)', 'Banner::delete/$1');

	//custom
	$subroutes->add('datatable', 'Banner::datatable');
	$subroutes->add('datatable/(:any)', 'Banner::datatable/$1');
	$subroutes->add('(:any)', 'Banner::detail/$1');
});
