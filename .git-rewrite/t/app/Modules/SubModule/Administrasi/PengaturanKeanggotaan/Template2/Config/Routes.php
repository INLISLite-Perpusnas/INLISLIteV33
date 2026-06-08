<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group(
	'master-template-kartu',
	['namespace' => 'Template\Controllers'],
	function ($subroutes) {
		$subroutes->add('', 'Template::index');
		$subroutes->add('index', 'Template::index');
		$subroutes->add('detail/(:any)', 'Template::detail/$1');
		$subroutes->add('edit/(:any)', 'Template::edit/$1');
		$subroutes->add('create', 'Template::create');
		$subroutes->add('delete/(:any)', 'Template::delete/$1');
		$subroutes->add('do_init', 'Template::do_init');
		$subroutes->add('do_upload', 'Template::do_upload');
		$subroutes->add('do_delete', 'Template::do_delete');
		$subroutes->add('flip', 'Template::flip');
		$subroutes->add('apply_status/(:any)', 'Template::apply_status/$1');
		$subroutes->add('export', 'Template::export');
		$subroutes->add('word', 'Template::word');
	}
);

$routes->group(
	'template',
	['namespace' => 'Template\Controllers'],
	function ($subroutes) {
		$subroutes->add('', 'Template::index');
		$subroutes->add('do_init', 'Template::do_init');
		$subroutes->add('do_upload', 'Template::do_upload');
		$subroutes->add('do_delete', 'Template::do_delete');
	}
);

$routes->group(
	'api/master-template-kartu',
	['namespace' => 'Template\Controllers\Api'],
	function ($subroutes) {
		//crud
		$subroutes->add('', 'Template::index');
		$subroutes->add('index', 'Template::index');
		$subroutes->add('detail/(:any)', 'Template::detail/$1');
		$subroutes->add('show/(:any)', 'Template::show/$1');
		$subroutes->add('create', 'Template::create');
		$subroutes->add('update/(:any)', 'Template::update/$1');
		$subroutes->add('delete/(:any)', 'Template::delete/$1');

		//custom
		$subroutes->add('datatable', 'Template::datatable');
		$subroutes->add('datatable/(:any)', 'Template::datatable/$1');
		$subroutes->add('upload_file', 'Template::upload_file');
		$subroutes->add('switch/(:any)', 'Template::switch/$1');
		$subroutes->add('(:any)', 'Template::detail/$1');
	}
);
