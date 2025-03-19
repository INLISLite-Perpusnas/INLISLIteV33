<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group(
	'master/kartuanggota', 
	['namespace' => 'KartuAnggota\Controllers'],
	function ($subroutes) {	
		$subroutes->add('', 'KartuAnggota::index');
		$subroutes->add('index', 'KartuAnggota::index');
		$subroutes->add('card', 'KartuAnggota::card');
		$subroutes->add('detail/(:any)', 'KartuAnggota::detail/$1');
		$subroutes->add('edit/(:any)', 'KartuAnggota::edit/$1');
		$subroutes->add('create', 'KartuAnggota::create');
		$subroutes->add('delete/(:any)', 'KartuAnggota::delete/$1');
		$subroutes->add('export', 'KartuAnggota::export');
		$subroutes->add('word', 'KartuAnggota::word');
	}
);

$routes->group(
	'kartuanggota', 
	['namespace' => 'KartuAnggota\Controllers'],
	function ($subroutes) {	
		$subroutes->add('apply_status/(:any)', 'KartuAnggota::apply_status/$1');
		$subroutes->add('do_init', 'KartuAnggota::do_init');
		$subroutes->add('do_upload', 'KartuAnggota::do_upload');
		$subroutes->add('do_delete', 'KartuAnggota::do_delete');
		$subroutes->add('flip', 'KartuAnggota::flip');
	}
);

$routes->group(
    'api/kartuanggota',
    ['namespace' => 'KartuAnggota\Controllers\Api'],
    function ($subroutes) {
        //crud
        $subroutes->add('', 'KartuAnggota::index');
        $subroutes->add('index', 'KartuAnggota::index');
        $subroutes->add('detail/(:any)', 'KartuAnggota::detail/$1');
        $subroutes->add('show/(:any)', 'KartuAnggota::show/$1');
        $subroutes->add('create', 'KartuAnggota::create');
        $subroutes->add('update/(:any)', 'KartuAnggota::update/$1');
        $subroutes->add('delete/(:any)', 'KartuAnggota::delete/$1');

        //custom
		$subroutes->add('datatable', 'KartuAnggota::datatable');
		$subroutes->add('datatable/(:any)', 'KartuAnggota::datatable/$1');
		$subroutes->add('switch/(:any)', 'KartuAnggota::switch/$1');
		$subroutes->add('upload_file', 'KartuAnggota::upload_file');
    }
);
