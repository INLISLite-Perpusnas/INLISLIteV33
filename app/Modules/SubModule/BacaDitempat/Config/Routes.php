<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('bacaditempat', ['namespace' => 'BacaDitempat\Controllers'], function ($subroutes) {
	$subroutes->add('', 'BacaDitempat::index');
	$subroutes->add('index', 'BacaDitempat::index');
	$subroutes->add('detail/(:any)', 'BacaDitempat::detail/$1');
	$subroutes->add('create', 'BacaDitempat::create');
	$subroutes->add('edit/(:any)', 'BacaDitempat::edit/$1');
	$subroutes->add('delete/(:any)', 'BacaDitempat::delete/$1');
	$subroutes->add('apply_status/(:any)', 'BacaDitempat::apply_status/$1');
	$subroutes->add('do_init', 'BacaDitempat::do_init');
	$subroutes->add('do_upload', 'BacaDitempat::do_upload');
	$subroutes->add('do_delete', 'BacaDitempat::do_delete');
	$subroutes->add('flip', 'BacaDitempat::flip');
});

$routes->group('api/bacaditempat', ['namespace' => 'BacaDitempat\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'BacaDitempat::detail/$1');
	$subroutes->add('create', 'BacaDitempat::create');
	$subroutes->add('edit/(:any)', 'BacaDitempat::edit/$1');
	$subroutes->add('delete/(:any)', 'BacaDitempat::delete/$1');

	//custom
	$subroutes->add('datatable', 'BacaDitempat::datatable');
	$subroutes->add('datatable/(:any)', 'BacaDitempat::datatable/$1');
});