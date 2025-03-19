<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/faq', ['namespace' => 'Faq\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Faq::index');
	$subroutes->add('index', 'Faq::index');
	$subroutes->add('detail/(:any)', 'Faq::detail/$1');
	$subroutes->add('edit/(:any)', 'Faq::edit/$1');
	$subroutes->add('create', 'Faq::create');
	$subroutes->add('delete/(:any)', 'Faq::delete/$1');
	$subroutes->add('do_init', 'Faq::do_init');
	$subroutes->add('do_upload', 'Faq::do_upload');
	$subroutes->add('do_delete', 'Faq::do_delete');
	$subroutes->add('flip', 'Faq::flip');
	$subroutes->add('apply_status/(:any)', 'Faq::apply_status/$1');
	$subroutes->add('export', 'Faq::export');
	$subroutes->add('thumb', 'Faq::thumb');
});

$routes->group('api/faq', ['namespace' => 'Faq\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Faq::index');
	$subroutes->add('index', 'Faq::index');
	$subroutes->add('detail/(:any)', 'Faq::detail/$1');
	$subroutes->add('show/(:any)', 'Faq::show/$1');
	$subroutes->add('create', 'Faq::create');
	$subroutes->add('update/(:any)', 'Faq::update/$1');
	$subroutes->add('delete/(:any)', 'Faq::delete/$1');

	//custom
	$subroutes->add('datatable', 'Faq::datatable');
	$subroutes->add('datatable/(:any)', 'Faq::datatable/$1');
	$subroutes->add('(:any)', 'Faq::detail/$1');
});
