<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-tag', ['namespace' => 'Tag\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Tag::index');
	$subroutes->add('index', 'Tag::index');
	$subroutes->add('create', 'Tag::create');
	$subroutes->add('edit/(:any)', 'Tag::edit/$1');
	$subroutes->add('delete/(:any)', 'Tag::delete/$1');
});

$routes->group('api/master-tag', ['namespace' => 'Tag\Controllers\Api'], function ($subroutes) {
	$subroutes->add('get_all_tags', 'Tag::get_all_tags');
	$subroutes->add('datatable', 'Tag::datatable');
	$subroutes->add('datatable/(:any)', 'Tag::datatable/$1');
	$subroutes->add('switch/(:any)', 'Tag::switch/$1');
	$subroutes->add('field_data_delete/(:any)/(:any)', 'Tag::field_data_delete/$1/$2');
	$subroutes->add('field_indicator1_delete/(:any)/(:any)', 'Tag::field_indicator1_delete/$1/$2');
	$subroutes->add('field_indicator2_delete/(:any)/(:any)', 'Tag::field_indicator2_delete/$1/$2');
});
