<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-nama-sumber-perolehan', ['namespace' => 'NamaSumberPerolehan\Controllers'], function ($subroutes) {
	$subroutes->get('', 'NamaSumberPerolehan::index');
	$subroutes->get('index', 'NamaSumberPerolehan::index');
	$subroutes->post('delete/(:num)', 'NamaSumberPerolehan::delete/$1', ['filter' => 'csrf']);
});

$routes->group('api/master-nama-sumber-perolehan', ['namespace' => 'NamaSumberPerolehan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('datatable', 'NamaSumberPerolehan::datatable');
	$subroutes->get('detail/(:num)', 'NamaSumberPerolehan::detail/$1');
	$subroutes->post('create', 'NamaSumberPerolehan::create', ['filter' => 'csrf']);
	$subroutes->post('edit/(:num)', 'NamaSumberPerolehan::edit/$1', ['filter' => 'csrf']);
});
