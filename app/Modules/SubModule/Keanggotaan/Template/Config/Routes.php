<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-template', ['namespace' => 'Template\Controllers'], function ($subroutes) {
	/*** Route Update for Template ***/
	$subroutes->add('', 'Template::index');
	$subroutes->add('index', 'Template::index');
	$subroutes->add('detail/(:any)', 'Template::detail/$1');
	$subroutes->add('create', 'Template::create');
	$subroutes->add('edit/(:any)', 'Template::edit/$1');
	$subroutes->add('delete/(:any)', 'Template::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Template::apply_status/$1');
	$subroutes->add('do_init', 'Template::do_init');
	$subroutes->add('do_upload', 'Template::do_upload');
	$subroutes->add('do_delete', 'Template::do_delete');
	$subroutes->add('flip', 'Template::flip');
});

$routes->group('api/master-template', ['namespace' => 'Template\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Template ***/
	$subroutes->add('detail/(:any)', 'Template::detail/$1');
	$subroutes->add('create', 'Template::create');
	$subroutes->add('edit/(:any)', 'Template::edit/$1');
	$subroutes->add('delete/(:any)', 'Template::delete/$1');

	//custom
	$subroutes->add('datatable', 'Template::datatable');
	$subroutes->add('datatable/(:any)', 'Template::datatable/$1');
	$subroutes->add('switch/(:any)', 'Template::switch/$1');
});

$routes->group('api/master-template', ['namespace' => 'Template\Controllers\Api'], function ($subroutes) {
	$subroutes->add('upload_file', 'Template::upload_file');
});
