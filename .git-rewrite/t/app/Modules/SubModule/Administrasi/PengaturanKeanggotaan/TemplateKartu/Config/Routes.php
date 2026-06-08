<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group(
	'master-template-kartu',
	['namespace' => 'TemplateKartuKartu\Controllers'],
	function ($subroutes) {
		$subroutes->add('', 'TemplateKartu::index');
		$subroutes->add('index', 'TemplateKartu::index');
		$subroutes->add('detail/(:any)', 'TemplateKartu::detail/$1');
		$subroutes->add('edit/(:any)', 'TemplateKartu::edit/$1');
		$subroutes->add('create', 'TemplateKartu::create');
		$subroutes->add('delete/(:any)', 'TemplateKartu::delete/$1');
		$subroutes->add('do_init', 'TemplateKartu::do_init');
		$subroutes->add('do_upload', 'TemplateKartu::do_upload');
		$subroutes->add('do_delete', 'TemplateKartu::do_delete');
		$subroutes->add('flip', 'TemplateKartu::flip');
		$subroutes->add('apply_status/(:any)', 'TemplateKartu::apply_status/$1');
		$subroutes->add('export', 'TemplateKartu::export');
		$subroutes->add('word', 'TemplateKartu::word');
	}
);

$routes->group(
	'template',
	['namespace' => 'TemplateKartuKartu\Controllers'],
	function ($subroutes) {
		$subroutes->add('', 'TemplateKartu::index');
		$subroutes->add('do_init', 'TemplateKartu::do_init');
		$subroutes->add('do_upload', 'TemplateKartu::do_upload');
		$subroutes->add('do_delete', 'TemplateKartu::do_delete');
	}
);

$routes->group(
	'api/master-template-kartu',
	['namespace' => 'TemplateKartuKartu\Controllers\Api'],
	function ($subroutes) {
		//crud
		$subroutes->add('', 'TemplateKartu::index');
		$subroutes->add('index', 'TemplateKartu::index');
		$subroutes->add('detail/(:any)', 'TemplateKartu::detail/$1');
		$subroutes->add('show/(:any)', 'TemplateKartu::show/$1');
		$subroutes->add('create', 'TemplateKartu::create');
		$subroutes->add('update/(:any)', 'TemplateKartu::update/$1');
		$subroutes->add('delete/(:any)', 'TemplateKartu::delete/$1');

		//custom
		$subroutes->add('datatable', 'TemplateKartu::datatable');
		$subroutes->add('datatable/(:any)', 'TemplateKartu::datatable/$1');
		$subroutes->add('upload_file', 'TemplateKartu::upload_file');
		$subroutes->add('switch/(:any)', 'TemplateKartu::switch/$1');
		$subroutes->add('(:any)', 'TemplateKartu::detail/$1');
	}
);
