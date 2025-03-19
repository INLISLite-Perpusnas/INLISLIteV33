<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('crud/catalog', ['namespace' => 'Crud\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Catalog::index');
	$subroutes->add('index', 'Catalog::index');
	$subroutes->add('add', 'Catalog::create');
	$subroutes->add('edit/(:any)', 'Catalog::edit/$1');
});

$routes->group('crud/order', ['namespace' => 'Crud\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Order::index');
	$subroutes->add('index', 'Order::index');
	$subroutes->add('add', 'Order::create');
	$subroutes->add('edit/(:any)', 'Order::edit/$1');
});

$routes->group('crud/product', ['namespace' => 'Crud\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Product::index');
	$subroutes->add('index', 'Product::index');
	$subroutes->add('add', 'Product::create');
	$subroutes->add('edit/(:any)', 'Product::edit/$1');
});
